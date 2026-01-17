<div class="row">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title mb-4">Recent Tickets</h4>

                <div class="table-responsive">
                    <table id="tickets_table" class="table table-bordered dt-responsive nowrap"
                        style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                        <thead>
                            <?php include 'Table/tickets_head.php';?>
                        </thead>
                        <tbody id="tickets-list">
                            <?php
                            $tickets = get_tickets($con, [
                                'limti' => 10,
                            ]);
                            include 'Table/tickets.php';
                            ?>

                        </tbody>

                    </table>
                </div>

            </div>
        </div>
    </div>
</div>
