#!C:/Users/mohds/AppData/Local/Programs/Python/Python39/python
import cgi,os
import docx
import pymysql
from pdf2docx import parse
import sys
import array as myarray

class Questions:

    def __init__(self):
        self.ans = [] 
        self.qns = []
        self.newq = []
        self.finalq = []
        self.newa = []
        self.tags = []
        self.a = []
        self.b = []
        self.c = []
        self.d = []
        self.e = []

    def load_file(self):     
        data = sys.argv[1]
        self.filename=data
        
    def readfile(self):
        doc = docx.Document(self.filename.replace("\\",""))
        qs = 0
        an = 0
        high=0
        low=0
        line=0
        for paragraph in doc.paragraphs:
            if paragraph.alignment!=1:
                l = 0
                m = 0
                if(('(a)' in paragraph.text) or ('(b)' in paragraph.text) or ('(c)' in paragraph.text) or ('(d)' in paragraph.text) or ('(e)' in paragraph.text)):        
                    if(line==1):
                        low=high
                        for i in range(low-1,high):
                            self.newq.append(self.qns[i])       
                    elif(line>1):
                        st=""
                        for i in range(low,high):
                            st+=self.qns[i]
                            low=low+1
                        self.newq.append(st)
                        low=high
                    self.ans.append(''.join(paragraph.text).replace('\t',"").replace("'",""))
                    if(l==0 and an<=qs):
                        an+=1
                    m+=1
                    line=0
                else:
                    line+=1
                    high+=1
                    self.qns.append(''.join(paragraph.text).replace('\t',"").replace("'",""))
                    if(m==0 and qs>=an):
                        qs+=1
                    l+=1
                    an=0
        for x in self.ans:
            x=x.lstrip()
            self.newa.append(x)
        self.newa.append(' ')      
        for x in self.newq:
            if '[' in x:
                y=x.split('[')[0]
                self.finalq.append(y)
                z=x.split('[')[-1]
                self.tags.append('['+z)
            else:
                self.finalq.append(x)
                self.tags.append('')

    def result(self):
        length = len(self.newa)
        for i in range(length):
            if('(d)' in self.newa[i]):
                if('(e)' in self.newa[i+1]):
                    e1=self.newa[i+1].split('(e)',1)[-1]
                    self.e.append(e1)
                else:
                    self.e.append('')
        for x in self.newa:
            if(x[:3]=='(a)'):
                if('(b)' in x):
                    y1=x.split('(a)',1)[-1]
                    y2=y1.split('(b)',1)[0]
                    self.a.append(y2)
                    z=x.split('(b)',1)[-1]
                    self.b.append(z)
                else:
                    y1=x.split('(a)',1)[-1]
                    self.a.append(y1)
            elif(x[:3]=='(b)'):
                y1=x.split('(b)',1)[-1]
                self.b.append(y1)
            elif(x[:3]=='(c)'):
                if('(d)' in x):
                    y1=x.split('(c)',1)[-1]
                    y2=y1.split('(d)',1)[0]
                    self.c.append(y2)
                    z=x.split('(d)',1)[-1]
                    self.d.append(z) 
                else:
                    y1=x.split('(c)',1)[-1]
                    self.c.append(y1)
            
            elif(x[:3]=='(d)'):
                y1=x.split('(d)',1)[-1]
                self.d.append(y1)
        n=len(self.finalq)
        questions = []
        options = []
        data =[]
        for i in range(n):
            questions.append(self.finalq[i])
            option =[]
            option.append(self.a[i])
            option.append(self.b[i])
            option.append(self.c[i])
            option.append(self.d[i])
            option.append(self.e[i])
            options.append(option)
            # dic={"Question":self.finalq[i],"Answer":{"a":self.a[i],"b":self.b[i],"c":self.c[i],"d":self.d[i],"e":self.e[i]}}
        print(questions)
        print(options)    
a = Questions()
a.load_file()
a.readfile()
a.result()