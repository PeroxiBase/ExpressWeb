#!/usr/local/bioinfo/src/python/current/bin/python
import MySQLdb,csv,json,resource,math
from sys import argv
import time as time_ #make sure we don't override time
import datetime
startScript=datetime.datetime.now().time()
def millis():
    return int(round(time_.time() * 1000))

print 'Start script:            ',millis(),startScript

username=argv[1]
filename=argv[2]
seuilOri=argv[3]
seuil=str(1-float(seuilOri))
#seuil=argv[3]

def readCSV(filename):
    res=[]
    freader= csv.reader(open(filename,'r'))
    for row in freader:
        res.append(row)
    return res

config={}
execfile("ExpressWeb.conf",config)
#seuilname=seuil.replace(".","_")
seuilname=seuilOri.replace(".","_")
groupFile=filename+"_"+seuilname+'_Cluster'
simFile=filename+"_Similarity"
dir=config['output_files']+username+"/"
print 'call readCSV:            ',millis()
sim=readCSV(dir+simFile)

## GET DATA FROM DataBase ##
print 'SQL query:               ',millis()
db = MySQLdb.connect(config['host'],config['dbUser'],config['dbPwd'],config['db'])
cursor = db.cursor()
sql1="SELECT a.*,b.Gene_Name FROM "+groupFile+" as a , "+filename+" as b WHERE a.Gene_ID=b.Gene_ID ORDER BY cluster"

# Groups
try:
    # Execute the SQL command
   cursor.execute(sql1)
   # Fetch all the rows in a list of lists.
   result = cursor.fetchall()
   groups=[]
   for row in result:
       groups.append(row)
except:
    print "Error: unable to fecth data"

# disconnect from server
db.close()
print 'SQL query close:         ',millis()
##################################################################################################

# Nodes Creation #
nodes=[]
print 'Nodes Creation:          ',millis()
for i in range(0,len(groups)):
    node={}
    node['id']=int(groups[i][0])
    node['label']=groups[i][3]
    node['group']=int(groups[i][2])
    node['cluster']=int(groups[i][1])
    node['title']="<p style='color:black' 'font-size:10px'>Gene Name : "+str(node['label'])+"</br>Cluster : "+str(node['cluster'])+"</p>"
    nodes.append(node)

# Edges construction #
groups={}
clusters={}
# INTRA Cluster edges #
print 'INTRA Cluster edges'
print 'n in nodes               ',millis()
for n in nodes:
    cluster= n['cluster']
    if cluster in clusters:
	clusters[cluster].append(n)
    else:
	clusters[cluster]=[]
	clusters[cluster].append(n)

print 'cluster in clusters:     ',millis()

for cluster in clusters:
    group = clusters[cluster][0]['group']
    if group in groups:
	groups[group].append(clusters[cluster])
    else:
	groups[group]=[]
	groups[group].append(clusters[cluster])

colored=[]
print 'group in groups:         ',millis()

for group in groups:
    clusters=groups[group]
    bestID1=0
    bestID2=0
    bestScore=0.0
    for cluster in clusters:
	bestID1=0
	bestID2=0
	bestScore=0.0
	for gene1 in cluster:
	    edges=[]    
	    for gene2 in cluster:
		if(gene1 != gene2):
		    id1=gene1['id']
		    id2=gene2['id']
		    score=float(sim[id1][id2+1])
		    edge={}
		    edge['from']=id1
		    edge['to']=id2                            
		    edge['value']=score
		    edges.append(edge)
	    edges=sorted(edges, key=lambda dct: dct['value'],reverse=True)
	    for edge in edges:
		reverseEdge={}
		reverseEdge['to']=edge['from']
		reverseEdge['from']=edge['to']
		reverseEdge['value']=edge['value']
	        if (edge not in colored) and (reverseEdge not in colored):
		    colored.append(edge)
		    break
edges=[]
print 'col in colored:          ',millis()

for col in colored:
    edge={}
    edge['color']='#FF8000'
    edge['from']=col['from']
    edge['to']=col['to']
    edge['length']=math.pow(col['value'],3)*100
    edge['label']=col['value']
    font={}
    font['color']='white'
    font['strokeWidth']=0
    font['size']=14
    edge['font']=font
    edge['width']=4
    edges.append(edge)


## INTER CLUSTERS edges ##
print 'INTER CLUSTERS edges'
print 'group in groups          ',millis()

for group in groups:
    clusters=groups[group]
    for cluster1 in clusters:
	meanScore=0.0
	bestMean=0.0
	bestID1=0
	bestID2=0
	bestScoreF=0
	for cluster2 in clusters:
	    if(cluster1 != cluster2):
		sumScore=0.0
		nbGene=0
		bestSubID1=0
		bestSubID2=0
		bestScore=0.0
		for gene1 in cluster1:
		    id1=gene1['id']
		    for gene2 in cluster2:
			id2=gene2['id']
			score=float(sim[id1][id2+1])
			sumScore+=score
			nbGene+=1
			if score > bestScore:
			    bestSubID1=id1
			    bestSubID2=id2
			    bestScore=score
		meanScore=sumScore/nbGene
		if meanScore > bestMean:
		    bestMean=meanScore
		    bestID1=bestSubID1
		    bestID2=bestSubID2
		    bestScoreF=bestScore
	
	edge={}		    
	edge['from']=bestID1
	edge['to']=bestID2
	edge['color']='#2ECCFA'
	edge['length']=math.pow(bestScoreF,4)*100
	arrows={}
	to={}
	to['enabled']=True
	to['scaleFactor']=1
	arrows['to']=to
	edge['arrows']=arrows
	edges.append(edge)

print 'End process:             ',millis()
print("Memory :")
print(str(resource.getrusage(resource.RUSAGE_SELF).ru_maxrss / 1000) + " MB")
print 'Success'

filename=filename+"_"+seuilname+".json"
fileNode=dir+'/Nodes'+filename
fileEdges=dir+'/Edges'+filename

## Write Files ##
print 'Write Files fileNode:    ',millis()

with open(fileNode,'wr') as f:
    json.dump(nodes,f)
    f.close
print 'Write Files fileEdges:   ',millis()
with open(fileEdges,'wr') as f:
    json.dump(edges,f)
    f.close
## End file signal ##
print 'Write Files EndJob_:     ',millis()
FileEndScript=open(dir+'/EndJob_'+filename,"w")
FileEndScript.write("End python")
endScript=datetime.datetime.now().time()
print 'End script:              ',millis(),endScript
FileEndScript.close
