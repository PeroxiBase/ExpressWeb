#!/usr/local/bioinfo/src/R/current/bin/Rscript
# Modify the previous shebang for your repertories #
 
# Load libraries and config file #
suppressPackageStartupMessages(library(dendextend))
library('RMySQL',lib.loc='/usr/local/bioinfo/src/R/R-3.1.2/lib64/R/library/')
library('Hmisc')
library('RJSONIO')
library('dendextend')
source("config.R")

## Read ARGS ##
args <- commandArgs(trailingOnly = TRUE)
username=args[1]
filename=args[2]
#seuil=as.numeric(args[3])
seuilOri=as.numeric(args[3])
seuil=1-seuilOri
dir=paste(output_files,username,sep="/")

## Connection and get data ##
#sprintf("user= %s,password= %s,host= %s,dbname= %s", user,password,host,dbname)
myDB <- dbConnect(RMySQL::MySQL(),user=user,password=password,host=host,dbname=dbname)
query1='SELECT * FROM'
query=paste(query1,filename,sep=" ")
res=dbSendQuery(myDB,query)

# Fetch data #
data <- fetch(res, n = -1) 
data=as.data.frame(data)
rNames=data[,1]
Gene_ID=data[,1]
data=data[,-c(1,2)]
data=data.matrix(data)
data=scale(data,center=T,scale=T)

############# Replicate col if ncol <= 4 for varclus ################
stock=data
if( ncol(data) <= 4){ 
  data=do.call(cbind, replicate(3, data, simplify=FALSE))
}
data2=t(data)

##################################
## Compute Clustering ##

#dist : pearson ; hoeffding ; spearman
#method : ward ; ward.D2 ; average

gene=varclus(data2, similarity="hoeffding",method="ward.D2",trans="none") # Clustering
sim=round(gene$sim,2) # reduct file size
sim=cbind(Gene_ID,sim)
colnames(sim)=c("Gene_ID",Gene_ID)
hclust=gene$hclust

hclust$height=sort(hclust$height)
max=max(hclust$height)

s1=seuil*max #user Threshold
s2=0.70*max #lowlevel Threshold

# Dendogramm Cut #
groupsGene=as.matrix(sapply(cutree(hclust, h=s1,order_clusters_as_data=F),as.numeric))
colors=as.matrix(sapply(cutree(hclust, h=s2,order_clusters_as_data=F),as.numeric))
groupFile=cbind(groupsGene,colors)
groupFile=cbind(Gene_ID,groupFile)
groupFile=groupFile[order(groupFile[,2]),]
colnames(groupFile)=c("Gene_ID","cluster","group")

stock=cbind(Gene_ID,stock)
res=cbind(stock,groupsGene)
res=res[order(res[,ncol(res)]),]
res=res[,-ncol(res)]
res=round(res,3)

#### Write Tables ####

## tables names ##
#seuil=as.character(seuil)
##seuil=sub("\\.","_", seuil)
#order=paste(seuil,"Order",sep="_")
#cluster=paste(seuil,"Cluster",sep="_")
seuilOri=as.character(seuilOri)
seuilOri=sub("\\.","_", seuilOri)
order=paste(seuilOri,"Order",sep="_")
cluster=paste(seuilOri,"Cluster",sep="_")

resName=paste(filename,order,sep="_")
clusterName=paste(filename,cluster,sep="_")
simName=paste(filename,"Similarity",sep="_")

## Convert to data frames ##

res=as.data.frame(res)
groupFile=as.data.frame(groupFile)
sim=as.data.frame(sim)

## Create tables ##
dbWriteTable(myDB,resName,res,row.names=FALSE,overwrite = T)
dbWriteTable(myDB,clusterName,groupFile,row.names=FALSE,overwrite = T)

## queries construction ##

q1="ALTER TABLE"
q2="ENGINE=MyISAM;"
q4="ADD PRIMARY KEY (Gene_ID);"

## order table ##
query1=paste(q1,resName,sep=" ")
queryType=paste(query1,q2,sep=" ")
queryKey=paste(query1,q4,sep=" ")
dbSendQuery(myDB,queryType) # change engine to MyISAM
dbSendQuery(myDB,queryKey) # set primary key to gene ID

## similarity Files ##
print(simName)
simdir=paste(dir,simName,sep="/")
print(simdir)
print("nrow")
print(nrow(sim))

# CSV FOR PYTHON #
#print(sim)
write.csv(sim,file=simdir)

## cluster table ##
query1=paste(q1,clusterName,sep=" ")
queryType=paste(query1,q2,sep=" ")
queryKey=paste(query1,q4,sep=" ")
dbSendQuery(myDB,queryType) # change engine to MyISAM
dbSendQuery(myDB,queryKey)# set primary key to gene ID

## File for End Script detection ##
test="Script OK"
fileTest=paste(filename,"Ended",sep="_")
fileTest=paste(dir,fileTest,sep="/")
print(fileTest)
write(test,file=fileTest)
