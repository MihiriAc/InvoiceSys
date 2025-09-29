<?php
include 'db.php';



?>
<form action="add.php" method="post" id="invoice">
<label for="cname">customer name:</label><br>    
<input type="text" id="cname" name="cname" value=""><br><br>
<label for="bill">Select Type</label>
<select form="invoice">
  <option value="bill">Bill</option>
  <option value="quotation">Quotation</option>
</select>
<button type="submit" name="add">Add Task</button>
</form>