<!DOCTYPE html>
<html>
<body>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"></script>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css">
<h1>All Enquiries</h1>
<table id="example" class="display" style="width:100%">
    <thead>
        <tr>
            <th>Name</th>
            <th>Position</th>
            <th>Company</th>
            <th>Email</th>
            <th>Date</th>
            <th>Phone</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php 
        if(count($enquiries)>0) {
            foreach($enquiries as $key=>$val) { ?>
                <tr>
                    <td><?= $val['full_name']?></td>
                    <td><?= $val['designation']?></td>
                    <td><?= $val['company_name']?></td>
                    <td><?= $val['phone']?></td>
                    <td><?= $val['email']?></td>
                    <td><?= $val['meeting_date']?></td>
                    <td><a href="<?=base_url()?>index.php/home/view_details/<?= $val['enquiry_id']?>">View</a></td>
                </tr>
        <?php 
            }
        } else { 
    ?>
            <tr>
                <td colspan="7"></td>
            </tr>

    <?php } ?>
    </tbody>
    <tfoot>
        <tr>
        <th>Name</th>
            <th>Position</th>
            <th>Company</th>
            <th>Email</th>
            <th>Date</th>
            <th>Phone</th>
            <th>Action</th>
        </tr>
    </tfoot>
</table>

</body>
<script type="text/javascript">
$(document).ready(function() {
    $('#example').DataTable();
});
</script>
</html>

