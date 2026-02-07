<?php

class Model
{
    protected static $table;
    protected $attributes = [];
    protected $fillable = [];
    protected $con;
    protected $wheres = [];

    public function __construct($db)
    {
        $this->con = $db;
    }

    /* Mass Assignment Protection */
    public function __set($key, $value)
    {
        if (in_array($key, $this->fillable)) {
            $this->attributes[$key] = $value;
        }
    }

    public function __get($key)
    {
        return $this->attributes[$key] ?? null;
    }

    /* ================= SAVE ================= */
    public function save()
    {
        $columns = implode(",", array_keys($this->attributes));
        $values  = "'" . implode("','", array_map([$this->con, 'real_escape_string'], $this->attributes)) . "'";

        $sql = "INSERT INTO " . static::$table . " ($columns) VALUES ($values)";
        return $this->con->query($sql);
    }

    /* ================= FIND ================= */
    public function find($id)
    {
        $sql = "SELECT * FROM " . static::$table . " WHERE id = " . (int)$id . " LIMIT 1";
        $res = $this->con->query($sql);

        if ($res && $res->num_rows) {
            $this->attributes = $res->fetch_assoc();
            return $this;
        }

        return null;
    }

    /* ================= WHERE ================= */
    public function where($column, $operator, $value)
    {
        $value = $this->con->real_escape_string($value);
        $this->wheres[] = "$column $operator '$value'";
        return $this;
    }

    public function first()
    {
        $sql = "SELECT * FROM " . static::$table;

        if (!empty($this->wheres)) {
            $sql .= " WHERE " . implode(" AND ", $this->wheres);
        }

        $sql .= " LIMIT 1";

        $res = $this->con->query($sql);

        if ($res && $res->num_rows) {
            $this->attributes = $res->fetch_assoc();
            return $this;
        }

        return null;
    }

    public function get()
    {
        $sql = "SELECT * FROM " . static::$table;

        if (!empty($this->wheres)) {
            $sql .= " WHERE " . implode(" AND ", $this->wheres);
        }

        $res = $this->con->query($sql);
        $data = [];

        while ($row = $res->fetch_assoc()) {
            $obj = new static($this->con);
            $obj->attributes = $row;
            $data[] = $obj;
        }

        return $data;
    }

    /* ================= UPDATE ================= */
    public function update($id)
    {
        if (empty($this->attributes)) {
            return false;
        }

        $sets = [];

        foreach ($this->attributes as $key => $val) {
            $val = $this->con->real_escape_string($val);
            $sets[] = "$key='$val'";
        }

        $sql = "UPDATE " . static::$table . " SET " . implode(",", $sets) . " WHERE id=" . (int)$id;
        return $this->con->query($sql);
    }
}
