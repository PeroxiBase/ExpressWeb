
              
	<!-- Intro Content -->
	<div id="row">
			
	<!-- Main Content -->
        <h2>Project Information</h2>
        <a href="<?php echo base_url();?>auth_public/manage_project">Manage Project</a><br /><br />

<?php if (! empty($message)) { ?>
        <div id="message">
                <?php echo $message; ?>
        </div>
<?php } 

//var_dump($project);
?>
        
        <?php echo form_open(current_url());	?>  	
        
      <!--  <div id="tabs"> -->
            <ul  class="nav nav-tabs" role="tablist">
                <li role="presentation" class="active"><a href="#tabs-1" role="tab" data-toggle="tab" aria-controls="tabs-1" >Working space</a></li>
                <li role="presentation"><a href="#tabs-2" role="tab" data-toggle="tab" aria-controls="tabs-2" >Project Details</a></li>
                <li role="presentation"><a href="#tabs-3" role="tab" data-toggle="tab" aria-controls="tabs-3" >Analysis</a></li>
                <li role="presentation"><a href="#tabs-4" role="tab" data-toggle="tab" aria-controls="tabs-4" >File Managment</a></li>
            </ul>
            
          <div class="tab-content">
          
            <div class="tab-pane active" id="tabs-1">
                <legend>Environment details</legend>
                <?php
                
                  $username= $this->session->username;
                  $working_project= $this->session->working_project;
                  $Directory="./assets/users/$username";
                  #$Directory="./assets/users/$username/$working_project";
                  $this->load->helper('directory');
                  $map = directory_map($Directory);
                  
                 # print "<pre>".print_r($project,1)."</pre>";
                ?>
            </div>
        
            <div class="tab-pane " id="tabs-2">
                <fieldset>
                                        
                <legend>Project Details</legend>
                
                <ul>
                    <li class="info_req">
                        <label for="project_name">Project Name:</label>
                        <?php echo set_value('update_project_name',$project['uprj_project_name']);?> 
                    </li>
                    <li class="info_req">
                        <label for="creator">Creator:</label>
                        <?php echo set_value('update_creator_name',$project['username']);?>
                    </li>
                    <li class="info_req">
                        <label for="creator">Comment:</label>
                        <?php echo set_value('update_project_comment',$project['uprj_project_comment']);?>
                    </li>
                    <li class="info_req">
                        <label for="start">Start (Y-M-D):</label>
                        <?php  echo set_value('update_date_start',$project['uprj_project_date_start']);?>
                    </li>
                    <li class="info_req">
                        <label for="end">End (Y-M-D):</label>
                        <?php echo set_value('update_date_end',$project['uprj_project_date_end']);?>
                    </li>
                    <li class="info_req">
                        <label for="visible">Visible:</label>
                        <?php echo $project['uprj_project_visibility'];
                    ?>
                    </li>
                        <li class="info_req">
                        <label for="shared">Shared:</label>
                    <?php 
                        echo $project['uprj_project_shared'];
                    ?>
                    </li>
                </ul>
                <!--	<li class="info_req">
                                <label for="creator">Member:</label>
                                <input type="text" id="creator" name="update_members" value="<?php echo set_value('update_members',$project['']);?>" class="tooltip_trigger"	title="." />
                        </li> 
                
                <ul>
                        
                        <li>
                                <hr/>
                                <label for="submit">Update Address:</label>
                                <input type="submit" name="update_project" id="submit" value="Submit" class="link_button large"/>
                                <input type="hidden" name="update_project_id" value="<?php echo $project['uprj_id'];?>"/>
                        </li>
                </ul>-->
                </fieldset>
            </div>
        
            <div class="tab-pane" id="tabs-3">
            <fieldset>
                <legend>Analysis</legend>
        <?php
#	$query=$this->auth_public->display_analysis( $project['uprj_id']);
        if($query->nbr!=0)
        {
         foreach($query->result as $row)
         {
           $pid = $row->pid; $project_id = $row->project_id; $user = $row->user;
           $section = $row->section; $action = $row->action;$Date = $row->Date; $uri = $row->uri; 
           $working_path = $row->working_path; 
           print " $project_id $user $section $action $Date $uri <br />";
         }
        }
        ?>
            <!--  <style>
                ul.main > li > ul {
                 display: none;
                }
              </style>
              <ul class='main'>
               <li>main item -click to show subs
                 <ul>
                  <li>sub1</li>
                  <li>sub2</li>
                  ...
                 </ul>
               </li>
              </ul>
              <ul class='main'>
               <li>main item II -click to show subs
                 <ul>
                  <li>sub1</li>
                  <li>sub2
                    <ul><li>kkk</li></ul>
                  </li>
                 </ul>
               </li>
              </ul>
              <script type="text/javascript">
              $('ul.main li').click(function() {
                $(this).children('ul').toggle();
              });
              </script>
              
              data: {"2014_05_13":["PEP.1399994729_end","PEP.1399994729.ph","PEP.1399994729.fa","PEP.1399994729.xml","PEP.1399994729.aln","result1399994729.zip","PEP.1399994729.fasta","PEP.1399994729_error.txt","PEP.1399994729_score.sc","PEP.1399994729.dnd"],"2014_05_14":["PEP.1400082199.ph","PEP.1400082199.fa","PEP.1400082199_error.txt","PEP.1400082199_score.sc","PEP.1400082199.dnd","PEP.1400082199.aln","PEP.1400082199_end","PEP.1400082199.fasta"],"2014_05_16":{"0":"result1400229796.zip","Clustal":{"1400233979":["PEP.1400233979.xml","PEP.1400233979.aln","PEP.1400233979_end","PEP.1400233979_error.txt","PEP.1400233979_score.sc","PEP.1400233979.ph","result1400233979.zip","PEP.1400233979.fasta"],"1400240750":["result1400240750.zip","PEP.1400082199.fa","PEP.1400082199.dnd","PEP.1400082199.aln","PEP.1400240750_error.txt","PEP.1400240750_end","PEP.1400082199.fasta","PEP.1400240750_score.sc"],"1400241011":["PEP.1400241011_end","PEP.1400241011_error.txt","PEP.1400241011_score.sc","result1400241011.zip","PEP.1400241011.fa","PEP.1400241011.dnd","PEP.1400241011.fasta","PEP.1400241011.ph","PEP.1400241011.aln"],"1400240608":["PEP.1400082199.fa","PEP.1400240608_end","result1400240608.zip","PEP.1400082199.dnd","PEP.1400240608_score.sc","PEP.1400082199.aln","PEP.1400082199.fasta","PEP.1400240608_error.txt"],"1400233980":[]}},"2014_05_15":[]};
                 $('#tt').tree({
                    
                  });
                  <div id="tree"></div>
             
              <script type="text/javascript">
           /*   var files = <?php #echo json_encode($map); 
           ?>;
              $("#tree").fancytree({
                   
                source: [
    {title: "Node 1", key: "1"},
    {title: "Folder 2", key: "2", folder: true, children: [
      {title: "Node 2.1", key: "3"},
      {title: "Node 2.2", key: "4"}
    ]},
     {title:"Node 3"},],
                checkbox: true,
              });
              $("#trees").fancytree();*/
              </script>
            

              <div id="trees">
                <ul id="treeData" style="display: none;">
                  <li id="1">Node 1
                  <li id="2" class="folder">Folder 2
                    <ul>
                      <li id="3">Node 2.1
                      <li id="4">Node 2.2
                    </ul>
                </ul>
              </div>
               -->
                </fieldset>
        </div>
        
        <div class="tab-pane" id="tabs-4">
        
          <!-- var timestamp = 1293683278;
var date = new Date(timestamp*1000);
var iso = date.toISOString().match(/(\d{2}:\d{2}:\d{2})/) -->
					
					 
                  
        <?php
            print "Current working directory: $Directory<br />\n"; 
            /*      print "<ul class=\"tree\">\n"; 
                  function print_dir($in,$depth,$Directory)
                  {
                   # global $Directory;
                    
                      foreach ($in as $k => $v)
                      {
                          if (!is_array($v))
                              echo "<p>",str_repeat("&nbsp;&nbsp;&nbsp;",$depth)," ",$v," [file]</p>";
                          else
                          {
                            //  echo "<p>",str_repeat("&nbsp;&nbsp;&nbsp;",$depth)," <b>",$k,"</b> [directory]</p>";
                            echo "<li>$k\n"; 
                            echo "<ul class=\"tree\">\n"; 
                            foreach($v as $file)
                            {
                              if (preg_match('/fa|fasta/',$file))
                              {
                                 echo "<li>".anchor(base_url()."gene/display_seq/$Directory/$k/$file",$file,'target="_blank"')."</li>\n";
                              }
                              else
                              {
                                echo "<li>".anchor(base_url()."$Directory/$k/$file",$file,'target="_blank"')."</li>\n";
                              }
                            }
                            echo "</ul>\n</li>\n"; 
                              //,print_dir($v,$depth+1)
                              
                          }
                      }
                  }
                  
                  print_dir($map,0, $Directory);
                  
                  print "</ul>";
                 */
                  ?>
                  
                   <div id="files">
                   <style type="text/css">
  /* Define custom width and alignment of table columns */
  #treetable {
    table-layout: fixed;
  }
  #treetable tr td:nth-of-type(1) {
    text-align: right;
  }
  #treetable tr td:nth-of-type(2) {
    text-align: center;
  }
  #treetable tr td:nth-of-type(3) {
    min-width: 100px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }
</style>

                  <script type="text/javascript">
              $(document).ready(function() {
                var files = <?php echo json_encode($map); ?>;
                var file_tree = build_file_tree(files);
                file_tree.appendTo('#files');
                function openFile(file) {
                    // do something with file
                    
                }
                function build_file_tree(files) {
                  
                  var tree = $('<ul class="tree">');
                  
                  for (x in files) {                    
                    if (typeof files[x] == "object") {
                      var span = $('<span>').html(x).appendTo(
                        $('<li class="tree">').appendTo(tree).addClass('folder')
                      );
                      var subtree = build_file_tree(files[x]).hide();
                      span.after(subtree);
                      span.click(function() {
                        $(this).parent().find('ul:first').toggle();
                      });
                      
                    } else {
                      var li =$('<li class="tree">').html(files[x]).appendTo(tree).addClass('file');
                      li.click(function() {
   //window.open('<?php echo base_url()."$Directory/"; ?>');

                        var route = $(this).parents(".folder").map(
                          function(val,i) {
                            return $(i).find('span').html();
                          }).get().reverse().join("/");
                        route=route + "/" + $(this).html();
                        if (route.substr(0,1)=="/") route=$.trim(route).substr(1,route.length);
                        var file = $(this).html();
                        if (file.match(/fa|fasta/))
                        {
                          window.open('<?php echo base_url()."gene/display_seq/$Directory/"; ?>'+route);
                        }
                       
                        else
                        {
                          window.open('<?php echo base_url()."$Directory/"; ?>'+route);
                        }
                      });
                      
                    }
                  }
                  return tree;
                  
                }
              });
              
              </script>
              </div>
         <!--          <script type="text/javascript">
                    $(function() {
                      $("ul > li >ul").click(function(event) {        
                              event.preventDefault();
                              $(this).find("li").toggle();                    
                          });
                      });
                    </script>
             -->
                </div>
                
        </div>
        <?php echo form_close();?>
        
        <?php
                  #//////////////////////////////////////////# 
               /*   
                  echo "<select name='yourfiles'>";


                  function show_dir_files($in,$path)
                  {
                   foreach ($in as $k => $v)
                    {
                    if (!is_array($v))
                    {?>
                    <option><?php echo $path,$v ?></option>
                     <?php }
                     else
                    {
                     print_dir($v,$path.$k.DIRECTORY_SEPARATOR);
                     }
                     }
                  }
                  
                  show_dir_files($map,'');  // call the function 
                  echo "</select>";
                   #//////////////////////////////////////////# 
                  
                  function dir_to_array($dir, $separator = DIRECTORY_SEPARATOR, $paths = 'relative') 
                  {
                      $result = array();
                      $cdir = scandir($dir);
                      foreach ($cdir as $key => $value)
                      {
                          if (!in_array($value, array(".", "..")))
                          {
                              if (is_dir($dir . $separator . $value))
                              {
                                  $result[$value] = dir_to_array($dir . $separator . $value, $separator, $paths);
                              }
                              else
                              {
                                  if ($paths == 'relative')
                                  {
                                      $result[] = $dir . '/' . $value;                    
                                  }
                                  elseif ($paths == 'absolute')
                                  {
                                      $result[] = base_url() . $dir . '/' . $value;
                                  }
                              }
                          }
                      }
                      return $result;
                  } 
                  
                  $res=dir_to_array($Directory);
                  print_r($res);
                  */
                  
                  	/*  if(is_dir("./assets/users/$username/$working_project")==true)
					  {
					    $dir_file= get_dir_file_info("./assets/users/$username/$working_project/",FALSE);//, $top_level_only = TRUE
					    foreach ($dir_file as $dir=>$files)
					    {
					      if (is_array($files))
					      {
					       // foreach ($files as $file=>$file2)
					        //{
					          print "Dir :".$files['relative_path']." file ".$files['name']." <br />";
					       // }
					      }
					      else
					      {
					        //print "Dir : $dir file $file<br />";
					      }
					    }
					    */
					   
                
             /*
              #//////////////////////////////////////////#
               $file_list = array();
                 $stack[] = $Directory;
              
                 while ($stack) {
                     $current_dir = array_pop($stack);
                     if ($dh = opendir($current_dir)) {
                         while (($file = readdir($dh)) !== false) {
                             if ($file !== '.' AND $file !== '..') {
                                 $current_file = "{$current_dir}/{$file}";
                                 $report = array();
                                 if (is_file($current_file)) {
                                     $file_list[] = "{$current_dir}/{$file}";
                                 } elseif (is_dir($current_file)) {
                                     $stack[] = $current_file;
                                     $file_list[] = "{$current_dir}/{$file}/";
                                 }
                             }
                         }
                     }
                 }
                 foreach ($file_list as $file=>$file2)
                 {
                   print " file  $file2<br />";
                 }
                 */
                 #//////////////////////////////////////////#
					?>
					
				