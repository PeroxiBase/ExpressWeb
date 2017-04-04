### Database settings
        
Before proceeding to configuration of your local copy of Expression Db , you need to create the database on your mysql server.  
You need grants privileges on database to create new user and database.

**Note:** 
> Replace <i>username,hostname,password</i> and <i>dbname</i> by your own value.  

**Note II:**
> Even if your mysql server is located on the same machine as your Apache server,  
     **Do not use** *'localhost'* as hostname.  
   Use '%' (for any host) to allow cluster to connect to your database.  
When you launch job on the cluster, *'localhost'* will be interpreted as local from cluster !! 

**Note III:**
>For genes annotation we use internal tables (Annotation and Toolbox) and external references datas.
We use PFAM (PFAM V30), GO, KO, KOG, KEGG (From KEGG Database Feb 2017) and PANTHER(V11) description references.
This tables are created and we provide sql data in separate files.
```
express_web.sql             Application file. Contains all the tables and data used by our software
reference_data.sql          SQL  data. 12Mb . Contains all external data (Kegg, Go, PFAM, PANTHER)

Ref_Enzymes.sql             SQl Data. 0.4Mb Kegg Enzymes only
Ref_GO.sql                  SQl Data. 3.2Mb Gene Ontology referece annotation only
Ref_KEGG.sql                SQl Data. 1.4Mb Kegg KO only
Ref_KOG.sql                 SQl Data. 0.4Mb Eukaryote Orthologous only
Ref_PANTHER.sql             SQl Data. 4.8Mb PANTHER only
Ref_PFAM.sql                SQl Data. 0.9Mb PFAM only
```

1. define a username with all privileges and grants on the new database (in example we use same name for database and username):  

  ```
    CREATE USER 'username'@'hostname' IDENTIFIED BY 'password';
    GRANT ALL PRIVILEGES ON `dbname`.* TO 'username'@'hostname' WITH GRANT OPTION;
    FLUSH PRIVILEGES ;
     
    ex:
    CREATE USER 'express_web'@'%' IDENTIFIED BY 'my_super_password';
    GRANT ALL PRIVILEGES ON `express_web`.* TO 'express_web'@'%' WITH GRANT OPTION;
    FLUSH PRIVILEGES;
    
 PARANOID

      With mysql server and apache server on same machine
      1) create user localhost with All privileges:
      GRANT USAGE ON *.* TO 'express_web'@'localhost' IDENTIFIED BY PASSWORD 'my_super_password';
      GRANT ALL PRIVILEGES ON `express_web`.* TO 'express_web'@'localhost' WITH GRANT OPTION;

      2) create user % with limited access for the cluster:
      GRANT USAGE ON *.* TO 'express_web'@'%.cluster.org'' IDENTIFIED BY PASSWORD 'my_super_password'; //any of cluster's node with dns record
      or
      GRANT USAGE ON *.* TO 'express_web'@'192.168.25.%' IDENTIFIED BY PASSWORD 'my_super_password'; // any of cluster's node by subnet @IP address

      GRANT SELECT, INSERT, UPDATE, DELETE, CREATE, DROP, INDEX, ALTER, CREATE TEMPORARY TABLES, LOCK TABLES, CREATE VIEW, SHOW VIEW ON `express_web`.* TO 'express_web'@'%';
```
    
2. Keep these credentials for next steps  
3. **If you change database name**  
3.1. edit express_web.sql (in install/sql/) and change 'express_web' by your database name
    ```
      --
      -- DataBase: `express_web`
      --
      CREATE DATABASE IF NOT EXISTS `express_web` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
      USE `express_web`;
    ```
4. import express_web.sql (in install/sql/) . Database will be created with table and user accounts   
        from command prompt:
        ```
        $ mysql -u username -p < install/sql/express_web.sql  
        ```
5. optionnal : import reference_data.sql(in install/sql/) . Add data to References tables (Enzymes, PFAm, GO..) (~ 12Mo)

        from command prompt:
        ``` 
        $ mysql -u username -p -b express_web < install/sql/reference_data.sql
        ```
