<?php
include 'db.php';



?>
<form action="add.php" method="post" id="invoice">
<label for="cname">customer name:</label><br>    
<input type="text" id="cname" name="cname" value=""><br><br>

<label for="jname">Job Name:</label><br>
<input type="text" id="jname" name="jname"><br><br>

<label for="type">Select Type</label>
<select name="type" id="type" form="invoice">
  <option value="invoice">Invoice</option>
  <option value="quotation">Quotation</option>
</select>

<button type="submit" name="add">Add</button>
</form>