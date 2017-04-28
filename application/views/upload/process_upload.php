<?php
/**
* The Expression Database.
*       view upload/process_upload.php
*@copyright Laboratoire de Recherche en Sciences Vegetales 2016-2020
*@author Bruno SAVELLI<savelli@lrsv.ups-tlse.fr>
*@author Sylvain PICARD<sylvain.picard@lrsv.ups-tlse.fr>
*@version 1.0
*@package ExpressWeb
*@subpackage view
*/
?>
<!-- //////////////    upload/process_upload  //////////////  -->
    
    <?php echo anchor(base_url()."create_table/upload_csv","back to upload form");?>    


<!--DIV RIGHT -->
<div id="row">
    <?php
    $this->session->unset_userdata('updated_table');
    $count_lines = count($data_columns);
    print "<h2>Pre-process file information</h2>\n";
    
    print "File name: $file_name <br />";
    print "File size: $file_size octets<br />";
    print "Header: $has_header<br />";
    print "PostProcessing: $type_data <br />";
    print "Nbr of lines: $info <hr />";
     print $this->session->flashdata('message');
    $i=1;
    $table_list="";
    foreach($existing_tables->result as $row)
    {
        $TableName  = $row['TableName'];;
        $GroupName  = $row['name'];
        $Organism   = $row['Organism'];
        $Submitter  = $row['Submitter'];
        $version    = $row['version'];
        $Root = $row['Root'];
        $table_list .= "$TableName,";
       if($i==1) 
       {
           print " Availaible Table for organism $Organism <br />";
           print "<table class=\"table table-hover\" width=600px>\n";
           print "  <thead>\n";
           print "      <tr><th>Table Name</th><th>Group</th><th>Submitter</th><th>version</th></tr>\n";
           print "  </thead>\n";
           print "  <tbody>\n";
       }
       if($Root ==1 && !preg_match("/Annot|Toolbox/",$TableName))
       print "      <tr><td>$TableName</td><td>$GroupName</td><td>$Submitter</td><td>$version</td></tr>\n";
           
       $i++;
    }
    $table_list = trim($table_list,1);
    print "  </tbody>\n";
    print "</table><br />\n";
  #  print "table_list:  $table_list<br />";
   #  $supplement ="";
    ################# type_data ############################
    switch ($post_process)
    {
        case 'replicate':
            $supplement ="<input type=\"text\" name=\"replicate[%d]\" value=\"\" size=\"10\" maxlength=\"10\" required />";
            $TitleSupp= "Replicate";
            break;
        case 'condition':
            $supplement ="<input type=\"text\" name=\"condition[%d]\" value=\"\" size=\"10\" maxlength=\"10\" required />";
            $TitleSupp = "Condition";
            break;
        case 'log':
            $supplement ="";
            $TitleSupp = "";
            break;
        case 'std':
            $supplement ="";
            $TitleSupp = "";
            break;
        case 'none':
            $supplement ="";
            $TitleSupp = "";
            break;    
    }

    #     print "data: <pre>".print_r($data_columns[0],1)."</pre> <br />";
    print form_open_multipart("create_table/create_table");     
    print form_hidden('file_name', $file_name);
    print form_hidden('Path', $Path);
    print form_hidden('master_group',$master_group);
    print form_hidden('organism', $organism);
    print form_hidden('separator', $separator);
    print form_hidden('type_data', $type_data);
    print form_hidden('post_process', $post_process);
    print form_hidden('limit', $limit);
    
    #########################
    $help_Drop= "<a class=\"glyphicon glyphicon-question-sign \" data-toggle=\"popover\" title=\"Force DROP\" data-content=\"DROP existing table. 
                Use this option if you miss -select a column value.<br />
                Resubmit this form:<br />Table will be dropped an recreated with new columns definition.<hr />
                <b>WARNING:</b> Don't try to delete an existing Table from here !!. Go to Expression Db menu to proceed  \" </a>";
    $help_Comment ="<a class=\"glyphicon glyphicon-question-sign \" data-toggle=\"popover\" title=\"Comment field\" 
                data-content=\"Value in Gene_Name column can't exceed $max_size characters. <br />If you want to keep this information, check this box<br />
                A comment field will be add to the table\"></a>";
    ########################
    print "<p>$required_tag Required input</p>\n";
    print "<table class=\"table-collapse\" width=\"700px\">\n";
    print "     <tr>\n";
    print "             <td>".form_label("Give a name to your table: ",'tableSql')." $required_tag</td>\n";
    print "             <td>${master_group}_<input type=text name='tableSql' id=tableSql required value='' /></td>\n";
    print "     </tr>\n";
    print "     <tr>\n";
    print "             <td>".form_label("Give a version number to your table: ",'version')." $required_tag</td>\n";
    print "             <td><input type=text name='version' required value='' /></td>\n";
    print "     </tr>\n";
    print "     <tr>\n";
    print "             <td>".form_label("Comment: ",'comment')." $required_tag</td>\n";
    print "             <td> <textarea name='comment' cols=60 rows=5 required></textarea></td>\n";
    print "     </tr>\n";
    #  print "Force decimal point for double value: <input type=radio name='decimal' required value='.' /> dot (.)
    #  <input type=radio name='decimal' required value=',' /> ,<br />";
    print "     <tr>\n";
    print "             <td>Use NULL for Empty Field:</td>\n";
    print "             <td> <input checked name=\"null_field\" class=nul_field type=\"checkbox\"  value='1' /></td>\n";
    print "     </tr>\n";
    print "     <tr>\n";
    print "             <td>Force DROP of existing table :</td>\n";
    print "             <td> <input name=\"force_dump\" class=nul_field type=\"checkbox\"  value='1' /> $help_Drop</td>\n";
    print "     </tr>\n";
    print "     <tr>\n";
    print "             <td colspan=\"2\"><b>WARNING</b> GeneName will be truncated at $max_size characters !</td>\n";
    print "     </tr>\n";
    print "     <tr>\n";
    print "             <td>Store full Name in comment field ?</td>\n";
    print "             <td> <input name=\"comment_geneName\" type=\"checkbox\" value='1' /> $help_Comment</td>\n";
    print "     </tr>";
    print "</table>\n";
    print "<br /><br />";
    print "<i>Displayed values in this table match first line of submited file $file_name </i><br /><br />\n";
    print "<table class=\"table-collapse\" border=1>\n";
    print "     <thead>\n";
    print "       <tr><th>N.</th><th>Title</th><th>Value</th>\n";
    if( $supplement)    print "             <th>$TitleSupp</th>";
    print "             <th>Type</th><th>Option</th><th>Size</th><th>Is Index</th>\n";
    print "           <th>Include<br><input class=inc type=\"checkbox\" checked /></th>\n";
    print "           <th>Required<br><input  class=require type=\"checkbox\" checked /></th>\n";
    print "       </tr>\n";
    print "     </thead>\n";
    print "     <tbody>\n";
    
        $i=1;
        
    foreach($header as $key=>$value)
    {
        $type = $max_value_col[$key]['type'];
        $option = $max_value_col[$key]['option'];
        $size = $max_value_col[$key]['size'];
        $type_select =  type_select($type);
        $option_select =  option_select($option);
        $index = "<input type='checkbox' name='is_index[]' value=\"$value\" />\n";
        
        print "<tr>";
        print "     <td>$i</td>";
        if($i==1) 
        {
            print "<td><input type=\"text\" name=\"col[$key]\" value=\"Gene_Name\" disabled />\n";
            print "<input type=\"hidden\" name=\"col[$key]\" value=\"Gene_Name\" /></td>\n";
            print "     <td>".$data_columns[$key] ."</td>\n";
            if( $supplement) print "    <td>&nbsp;</td>\n";            
        }
        else
        {
            print "<td><input type=\"text\" name=\"col[$key]\" value=\"$value\" /></td>\n";
            print "     <td>".$data_columns[$key] ."</td>\n";
            if( $supplement) print "    <td>".sprintf($supplement,$key)."</td>\n";
            
        }
        print "     <td>$type_select</td>\n";
        print "     <td>$option_select</td>\n";
        
        if($i==1)            
        {   
            if($size != $max_size) $size = $max_size;
            print "     <td><input type=\"text\" name=\"SqlSize[]\" value=\"$size\" size=\"5\"/ max =\"$max_size\" /></td>\n";
            print "     <td><input type='checkbox' name='is_index[]' value=\"1\" checked disabled /></td>\n";
            print "     <td><input type=\"checkbox\" class=\"finc\" id=\"finc_$key\" name=\"include[$key]\"  value=\"1\" checked disabled />
            <input type=\"hidden\" class=\"finc\" name=\"include[$key]\"  value=\"1\" checked  />
            </td>\n";
            print "     <td><input type=\"checkbox\" id=\"freq_$key\" class=\"freq\"  name=\"require[$key]\"  value=\"1\" checked disabled />
            <input type=\"hidden\" class=\"freq\"  name=\"require[$key]\"  value=\"1\" checked >
            </td>\n";
        }
        else 
        {
            print "     <td><input type=\"text\" name=\"SqlSize[]\" value=\"$size\" size=\"5\" /></td>\n";
            print "     <td>$index</td>\n";
            print "     <td><input class=\"finc\"  id=\"finc_$key\" name=\"include[$key]\" value=\"1\" type=\"checkbox\" checked /></td>\n";
            print "     <td><input class=\"freq\"  id=\"freq_$key\" name=\"require[$key]\"value=\"1\" type=\"checkbox\" checked /></td>\n";
        }
        print "</tr>\n";
        $i++;
    }
    print "</tbody>\n";
    print "</table>\n";
    print "<input name=\"Do_Sql\" value='1' type=\"hidden\" />";
    print form_submit( 'submit', 'Submit','id=submit'); 
    print form_reset( 'reset', 'Reset');
    
    print form_close();
    
  
    $max_mem= memory_get_usage(TRUE);
    print "<br /><b>Note</b> Value in column <b>Size</b> indicate max value found for this column in your whole file !! <br />\n";
    print "This may not reflect value displayed in this table (first line of data)<br />\n";
    print  "<hr />back parse memory_get_usage TRUE: $max_mem<hr />";
    
?>
</div>
<!--END DIV RIGHT -->
<?php
function type_select($type)
{
    $values=array('VARCHAR' => 'VarChar' ,'CHAR' => 'Char' ,'NUMERIC' => 'Numeric' ,
        'DOUBLE' => 'Double' ,'INT' => 'Int' ,'DATE' => 'Date' ,
        'TINYINT' => 'TinyInt' ,'SMALLINT' => 'SmallInt' ,'MEDIUMINT' => 'MediumInt' );
    
     $data ="       <select id=\"ftype1\" name =\"SqlType[]\" title=\"Choose data type of column\">";
     foreach($values as $key=>$val)
     { 
         if($key == $type)
                $data .=" <option value=\"$key\" selected=\"selected\">$val</option>";
         else
            $data .=" <option value=\"$key\">$val</option>";
     }
  $data .="      </select>\n";   
  return $data;
}

function option_select($option)
{
    $values=array('' =>'','UNSIGNED' => 'UNSIGNED' ,'SIGNED' => 'SIGNED'  );
    
     $data ="       <select id=\"ftype2\" name =\"SqlOption[]\" title=\"Choose data type of column\">";
     foreach($values as $key=>$val)
     { 
         if($key == $option)
                $data .=" <option value=\"$key\" selected=\"selected\">$val</option>";
         else
            $data .=" <option value=\"$key\">$val</option>";
     }
  $data .="      </select>\n";   
  return $data;
}
?>

<script type="text/javascript">
$(document).ready(function() {
    $(".inc").change(function () 
    { console.log("here i am: " + this.name + " value = " + $(this).val() );
        var key=2;
        $("input:checkbox.finc").prop('checked',this.checked);
        $("input:checkbox.freq").prop('checked',this.checked);
    });
      
    $(".require").change(function () 
    {
        $("input:checkbox.freq").prop('checked',this.checked);
    });
    
    $("input[id^='finc_']").change(function () 
    {      
        var key=$(this).attr('id').slice(5);
        
      // alert("id inc:"+key);
        $("#freq_"+key).prop('checked',this.checked);
    });
    
    $("#fheader").click(function()
    {
        $(".nlines_header").toggle();
    
    });
    
    $("#other").click(function()
       {
           $("input[name=separator_char]").prop('required',true);
       });
    
    $('[data-toggle="popover"]').popover({
        placement : 'top',
        trigger: 'hover',
        html: true
    });
    //  check if table name not in existing table name stored in Db
 /*   var table_list = new Array("<?php echo $table_list; ?>");
 //alert("Non valid names:"+ table_list);

    $("#submit").click(function(e){
              var id_val=$("#tableSql").val();
    e.preventDefault();
   // alert(isValidState($("#tableSql").val()));
    });
    function isValidState(st)
    {
        var id_val=$("#tableSql").val();
         for (var i = 0; i < table_list.length; i++) {
            if (table_list[i] === id_val) {
             //   alert('Value exist');
                 return "WARNING: You use the same name "+st+" than an existing table "+table_list+" in Database!\nPlease choose an another name\n";
            }
            
         }
    }
    */
});
</script>
<!-- //////////////    upload/process_upload  //////////////  -->
