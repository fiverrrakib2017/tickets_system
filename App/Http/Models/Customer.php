<?php
if (!isset($_SERVER['DOCUMENT_ROOT']) || $_SERVER['DOCUMENT_ROOT'] == '') {
    $_SERVER['DOCUMENT_ROOT'] = dirname(__DIR__); 
}
include $_SERVER['DOCUMENT_ROOT'] . '/App/Http/Core/Model.php';
class Customer extends Model
{
    protected static $table = 'customers';

    protected $fillable = [
        'customer_name',
        'customer_email',
        'username',
        'password',
        'pop_id',
        'customer_type_id',
        'customer_vlan',
        'ping_ip',
        'port',
        'ping_ip_status',
        'ping_sent',
        'ping_received',
        'ping_lost',
        'ping_min_ms',
        'ping_max_ms',
        'ping_avg_ms',
        'last_ping_at',
        'offline_since',
        'offline_duration',
        'private_customer_ip',
        'status',
        'profile_image',
        'total',
        'service_type',
        'customer_link',
        'created_at',
        'nid_file',
        'service_agreement_file',
        'service_customer_type'
    ];
}

