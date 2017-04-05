# ADMIN GUIDE

ExpressWeb use an access management system.
Users and Dataset(expression data) belong to groups who gives them access's rights.

Three Master Group (demo,members,admin) are defined and gives the following rights:
- **demo**  
  * use Dataset in group 'demo'  
  * can display clustering result.  
  * min
    * no Profile management  
    * no rights to launch cluster jobs nor create sub-datasets  
- **members**  
  * use all Dataset in group 'demo' or 'members'  
  * if user belong to another group, gains access to Dataset in this group  
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

### Users account Menu  
Admin can create users and group via this menu.  
External users may ask for an account. They use 'Sign Up' menu.  
Request will be send to SuperAdmin who can activate or not the account. See below.  

1. Select 'Users Accounts' tab and click on 'Manage User Accounts'  
  1.1. Edit 
       - click on edit link.  
       - **Note** Login Name is used to create users directories and store uploaded and saved files. 
       If you change it , you may create several directories ... but user access only directory matching is login.  
  1.2. Activate  
      -  click on 'Status' link. Active or Inactive current login  
      -  user will receive an email to renew is password.  
      -  easy way to activate user or deny login.   
1.3.  Create user  
      -  Fill all applicable fields and submit form.  
      -  Active user . An email will be send to user.  
2.  Select 'Users Groups' tab and clik on 'Manage User Groups'  
  2.1.  Edit  
    -  click on edit link.  
    -  enter group name and description
    -  **Note**  The membership of a user to a group is referenced by an internal number.  
       changing group name doesn't affect user membership.   
  2.2.  Create group  (Or from Manage User Accounts/Create a new group )  
    -  click on create link.  
    -  enter group name and description  
  2.3.  Modify users or tables group membership  
    
## Database management

From **Db admin** menu five tabs allow database and data management:  

### Manage Table

1.  Manage table Details  
   Display all tables (DataSet, clustering results (Cluster & Order) and Annotation) status.  
   Tables belong to a master group ( between brackets [admin] ) and a specific organism.  
   
   | TableName | Organism	| Submitter	| version	| Groups	| Level	| Action |
   | --- | --- | --- | --- | --- | --- | --- |
 | Annotation_3 |	Rhizophagus irregularis |	administrator  |	1  |	[admin]  |	Root  |	Ed. |
 | Annotation_Myco_AnnotTest  |	Rhizophagus irregularis  |	administrator  |	1  |	[admin] |	Root  |	Ed. |
 | Myco_AnnotTest  |	Rhizophagus irregularis  |	administrator  |	1  |	[admin]  members  Demo 	 | Root  | 	Ed. |  

  
2.  Remove table and dependencies  
3.  Regenerate tables annotation  


### User Access

1.  Manage users access  

### Convert Data

1.  Generate DataSet  

### Annotations

1.  Import  
  1.1.  Import annotation from Phytozom  
  1.2.  Create annotation  
  1.3.  Import Toolbox

2.  Edit  
  2.1.  Upgrade annotation from Phytozom  
  2.2.  Upgrade annotation  

### Manage organisms

1.  Manage organisms  

### Import data

### Renew data

 
