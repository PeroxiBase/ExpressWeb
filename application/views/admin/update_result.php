<?php
/**
* The Expression Database.
*       view admin/update_result.php
* view database operations results
*@copyright Laboratoire de Recherche en Sciences Vegetales 2016-2020
*@author Bruno SAVELLI<savelli@lrsv.ups-tlse.fr>
*@author Sylvain PICARD<sylvain.picard@lrsv.ups-tlse.fr>
*@version 1.0
*@package ExpressWeb
*@subpackage views
*/
?>
<!-- //////////////    admin/update_result  //////////////  -->

<div class="col-md-8 "> 
         <a href="<?php print base_url(); ?>admin/<?php print $return_action; ?>">back to Admin</a><br /><br />

<!-- ##### avoid resubmit on admin/remove tables !! ############# -->

        <?php $_SESSION['update_result']= '1'; ?>
        <?php print $update_result; 
        #print "<pre>".print_r( $POST,1)."</pre>";
        ?>
</div><!-- End DIV rows -->

<!-- //////////////    End admin/update_result  //////////////  -->
