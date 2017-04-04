# ADMIN GUIDE

ExpressWeb use an access management system.
Users and Dataset belong to groups who gives them access's rights.

Three Master Group (demo,members,admin) are defined and gives the following rights:
- **demo**  
  * use Dataset in group 'demo'  
  * can display clustering result.  
  * min
    * no Profile management  
    * no rights to launch cluster jobs nor create sub-datasets  
- **members**  
  * use all Dataset in group 'demo' or 'member'  
  * if user belong to another group, gains access to group Dataset  
  * can launch cluster job, create sub-dataset  
  * access to Profile and Data management  
  * min
     * no access to Dataset management
- **admin**  
  same as members  
  * plus  
      * import annotation. renew Dataset annotation or create new one.  
      * admin users . create users, groups.  
      * admin tables. remove tables and dependancies.  
      * cluster management and ability to kill running process  
    

## Users and Groups management

**Menu 'Users account'**  
Admin can create users and group via this menu.  
1. Select 'Users accounts' tab and click on 'Manage User Accounts'  
  1.1 Edit 
       - click on edit link.  
       - **Note** Login Name is used to create users directories and store uploaded and saved files. 
       If you change it , you may create several directories ... and user 
     

## Database management

### Import data

### Renew data

### Remove tables and dependnancies
