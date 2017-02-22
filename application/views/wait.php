<script>
 $(function(){
	$('#testForm').submit()
});
</script>
<?php 
$attributes=array('id'=>'testForm');
echo form_open_multipart('visual/show',$attributes);
echo form_hidden('pid', $pid);
echo form_hidden('option', $option);
echo form_hidden('hf',$h);
echo form_hidden('mf',$m);
echo form_hidden('sf',$s);
?>
