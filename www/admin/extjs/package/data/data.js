/*
 * Ext JS Library 1.0
 * Copyright(c) 2006-2007, Ext JS, LLC.
 * licensing@extjs.com
 * 
 * http://www.extjs.com/license
 */


Ext.data.SortTypes={none:function(s){return s;},stripTagsRE:/<\/?[^>]+>/gi,asText:function(s){return String(s).replace(this.stripTagsRE,"");},asUCText:function(s){return String(s).toUpperCase().replace(this.stripTagsRE,"");},asUCString:function(s){return String(s).toUpperCase();},asDate:function(s){if(!s){return 0;}
if(s instanceof Date){return s.getTime();}
return Date.parse(String(s));},asFloat:function(s){var val=parseFloat(String(s).replace(/,/g,""));if(isNaN(val))val=0;return val;},asInt:function(s){var val=parseInt(String(s).replace(/,/g,""));if(isNaN(val))val=0;return val;}};

Ext.data.Record=function(data,id){this.id=(id||id===0)?id:++Ext.data.Record.AUTO_ID;this.data=data;};Ext.data.Record.create=function(o){var f=function(){f.superclass.constructor.apply(this,arguments);};Ext.extend(f,Ext.data.Record);var p=f.prototype;p.fields=new Ext.util.MixedCollection(false,function(field){return field.name;});for(var i=0,len=o.length;i<len;i++){p.fields.add(new Ext.data.Field(o[i]));}
f.getField=function(name){return p.fields.get(name);};return f;};Ext.data.Record.AUTO_ID=1000;Ext.data.Record.EDIT='edit';Ext.data.Record.REJECT='reject';Ext.data.Record.COMMIT='commit';Ext.data.Record.prototype={dirty:false,editing:false,error:null,modified:null,join:function(store){this.store=store;},set:function(name,value){if(this.data[name]==value){return;}
this.dirty=true;if(!this.modified){this.modified={};}
if(typeof this.modified[name]=='undefined'){this.modified[name]=this.data[name];}
this.data[name]=value;if(!this.editing){this.store.afterEdit(this);}},get:function(name){return this.data[name];},beginEdit:function(){this.editing=true;this.modified={};},cancelEdit:function(){this.editing=false;delete this.modified;},endEdit:function(){this.editing=false;if(this.dirty&&this.store){this.store.afterEdit(this);}},reject:function(){var m=this.modified;for(var n in m){if(typeof m[n]!="function"){this.data[n]=m[n];}}
this.dirty=false;delete this.modified;this.editing=false;if(this.store){this.store.afterReject(this);}},commit:function(){this.dirty=false;delete this.modified;this.editing=false;if(this.store){this.store.afterCommit(this);}},hasError:function(){return this.error!=null;},clearError:function(){this.error=null;}};

Ext.data.Store=function(config){this.data=new Ext.util.MixedCollection(false);this.data.getKey=function(o){return o.id;};this.baseParams={};this.paramNames={"start":"start","limit":"limit","sort":"sort","dir":"dir"};Ext.apply(this,config);if(this.reader&&!this.recordType){this.recordType=this.reader.recordType;}
this.fields=this.recordType.prototype.fields;this.modified=[];this.events={datachanged:true,add:true,remove:true,update:true,clear:true,beforeload:true,load:true,loadexception:true};if(this.proxy){this.relayEvents(this.proxy,["loadexception"]);}
this.sortToggle={};Ext.data.Store.superclass.constructor.call(this);};Ext.extend(Ext.data.Store,Ext.util.Observable,{remoteSort:false,lastOptions:null,add:function(records){records=[].concat(records);for(var i=0,len=records.length;i<len;i++){records[i].join(this);}
var index=this.data.length;this.data.addAll(records);this.fireEvent("add",this,records,index);},remove:function(record){var index=this.data.indexOf(record);this.data.removeAt(index);this.fireEvent("remove",this,record,index);},removeAll:function(){this.data.clear();this.fireEvent("clear",this);},insert:function(index,records){records=[].concat(records);for(var i=0,len=records.length;i<len;i++){this.data.insert(index,records[i]);records[i].join(this);}
this.fireEvent("add",this,records,index);},indexOf:function(record){return this.data.indexOf(record);},indexOfId:function(id){return this.data.indexOfKey(id);},getById:function(id){return this.data.key(id);},getAt:function(index){return this.data.itemAt(index);},getRange:function(start,end){return this.data.getRange(start,end);},storeOptions:function(o){o=Ext.apply({},o);delete o.callback;delete o.scope;this.lastOptions=o;},load:function(options){options=options||{};if(this.fireEvent("beforeload",this,options)!==false){this.storeOptions(options);var p=Ext.apply(options.params||{},this.baseParams);if(this.sortInfo&&this.remoteSort){var pn=this.paramNames;p[pn["sort"]]=this.sortInfo.field;p[pn["dir"]]=this.sortInfo.direction;}
this.proxy.load(p,this.reader,this.loadRecords,this,options);}},reload:function(options){this.load(Ext.applyIf(options||{},this.lastOptions));},loadRecords:function(o,options,success){if(!o||success===false){if(success!==false){this.fireEvent("load",this,[],options);}
if(options.callback){options.callback.call(options.scope||this,[],options,false);}
return;}
var r=o.records,t=o.totalRecords||r.length;for(var i=0,len=r.length;i<len;i++){r[i].join(this);}
if(!options||options.add!==true){this.data.clear();this.data.addAll(r);this.totalLength=t;this.applySort();this.fireEvent("datachanged",this);}else{this.totalLength=Math.max(t,this.data.length+r.length);this.data.addAll(r);}
this.fireEvent("load",this,r,options);if(options.callback){options.callback.call(options.scope||this,r,options,true);}},loadData:function(o,append){var r=this.reader.readRecords(o);this.loadRecords(r,{add:append},true);},getCount:function(){return this.data.length||0;},getTotalCount:function(){return this.totalLength||0;},getSortState:function(){return this.sortInfo;},applySort:function(){if(this.sortInfo&&!this.remoteSort){var s=this.sortInfo,f=s.field;var st=this.fields.get(f).sortType;var fn=function(r1,r2){var v1=st(r1.data[f]),v2=st(r2.data[f]);return v1>v2?1:(v1<v2?-1:0);};this.data.sort(s.direction,fn);if(this.snapshot&&this.snapshot!=this.data){this.snapshot.sort(s.direction,fn);}}},setDefaultSort:function(field,dir){this.sortInfo={field:field,direction:dir?dir.toUpperCase():"ASC"};},sort:function(fieldName,dir){var f=this.fields.get(fieldName);if(!dir){if(this.sortInfo&&this.sortInfo.field==f.name){dir=(this.sortToggle[f.name]||"ASC").toggle("ASC","DESC");}else{dir=f.sortDir;}}
this.sortToggle[f.name]=dir;this.sortInfo={field:f.name,direction:dir};if(!this.remoteSort){this.applySort();this.fireEvent("datachanged",this);}else{this.load(this.lastOptions);}},each:function(fn,scope){this.data.each(fn,scope);},getModifiedRecords:function(){return this.modified;},filter:function(property,value){if(!value.exec){value=String(value);if(value.length==0){return this.clearFilter();}
value=new RegExp("^"+Ext.escapeRe(value),"i");}
this.filterBy(function(r){return value.test(r.data[property]);});},filterBy:function(fn,scope){var data=this.snapshot||this.data;this.snapshot=data;this.data=data.filterBy(fn,scope);this.fireEvent("datachanged",this);},clearFilter:function(){if(this.snapshot&&this.snapshot!=this.data){this.data=this.snapshot;delete this.snapshot;this.fireEvent("datachanged",this);}},afterEdit:function(record){if(this.modified.indexOf(record)==-1){this.modified.push(record);}
this.fireEvent("update",this,record,Ext.data.Record.EDIT);},afterReject:function(record){this.modified.remove(record);this.fireEvent("update",this,record,Ext.data.Record.REJECT);},afterCommit:function(record){this.modified.remove(record);this.fireEvent("update",this,record,Ext.data.Record.COMMIT);},commitChanges:function(){var m=this.modified.slice(0);this.modified=[];for(var i=0,len=m.length;i<len;i++){m[i].commit();}},rejectChanges:function(){var m=this.modified.slice(0);this.modified=[];for(var i=0,len=m.length;i<len;i++){m[i].reject();}}});

Ext.data.SimpleStore=function(config){Ext.data.SimpleStore.superclass.constructor.call(this,{reader:new Ext.data.ArrayReader({id:config.id},Ext.data.Record.create(config.fields)),proxy:new Ext.data.MemoryProxy(config.data)});this.load();};Ext.extend(Ext.data.SimpleStore,Ext.data.Store);

Ext.data.Connection=function(config){Ext.apply(this,config);this.events={"beforerequest":true,"requestcomplete":true,"requestexception":true};Ext.data.Connection.superclass.constructor.call(this);};Ext.extend(Ext.data.Connection,Ext.util.Observable,{timeout:30000,request:function(options){if(this.fireEvent("beforerequest",this,options)!==false){var p=options.params;if(typeof p=="object"){p=Ext.urlEncode(Ext.apply(options.params,this.extraParams));}
var cb={success:this.handleResponse,failure:this.handleFailure,scope:this,argument:{options:options},timeout:this.timeout};var method=options.method||this.method||(p?"POST":"GET");var url=options.url||this.url;if(this.autoAbort!==false){this.abort();}
if(method=='GET'&&p){url+=(url.indexOf('?')!=-1?'&':'?')+p;p='';}
this.transId=Ext.lib.Ajax.request(method,url,cb,p);}else{if(typeof options.callback=="function"){options.callback.call(options.scope||window,options,null,null);}}},isLoading:function(){return this.transId?true:false;},abort:function(){if(this.isLoading()){Ext.lib.Ajax.abort(this.transId);}},handleResponse:function(response){this.transId=false;var options=response.argument.options;this.fireEvent("requestcomplete",this,response,options);if(typeof options.callback=="function"){options.callback.call(options.scope||window,options,true,response);}},handleFailure:function(response,e){this.transId=false;var options=response.argument.options;this.fireEvent("requestexception",this,response,options,e);if(typeof options.callback=="function"){options.callback.call(options.scope||window,options,false,response);}}});

Ext.data.Field=function(config){if(typeof config=="string"){config={name:config};}
Ext.apply(this,config);if(!this.type){this.type="auto";}
var st=Ext.data.SortTypes;if(typeof this.sortType=="string"){this.sortType=st[this.sortType];}
if(!this.sortType){switch(this.type){case"string":this.sortType=st.asUCString;break;case"date":this.sortType=st.asDate;break;default:this.sortType=st.none;}}
var stripRe=/[\$,%]/g;if(!this.convert){var cv,dateFormat=this.dateFormat;switch(this.type){case"":case"auto":case undefined:cv=function(v){return v;};break;case"string":cv=function(v){return String(v);};break;case"int":cv=function(v){return v!==undefined&&v!==null&&v!==''?parseInt(String(v).replace(stripRe,""),10):'';};break;case"float":cv=function(v){return v!==undefined&&v!==null&&v!==''?parseFloat(String(v).replace(stripRe,""),10):'';};break;case"bool":case"boolean":cv=function(v){return v===true||v==="true"||v==1;};break;case"date":cv=function(v){if(!v){return'';}
if(v instanceof Date){return v;}
if(dateFormat){if(dateFormat=="timestamp"){return new Date(v*1000);}
return Date.parseDate(v,dateFormat);}
var parsed=Date.parse(v);return parsed?new Date(parsed):null;};break;}
this.convert=cv;}};Ext.data.Field.prototype={dateFormat:null,defaultValue:"",mapping:null,sortType:null,sortDir:"ASC"};

Ext.data.DataReader=function(meta,recordType){this.meta=meta;this.recordType=recordType instanceof Array?Ext.data.Record.create(recordType):recordType;};Ext.data.DataReader.prototype={};

Ext.data.DataProxy=function(){this.events={beforeload:true,load:true,loadexception:true};Ext.data.DataProxy.superclass.constructor.call(this);};Ext.extend(Ext.data.DataProxy,Ext.util.Observable);

Ext.data.MemoryProxy=function(data){Ext.data.MemoryProxy.superclass.constructor.call(this);this.data=data;};Ext.extend(Ext.data.MemoryProxy,Ext.data.DataProxy,{load:function(params,reader,callback,scope,arg){params=params||{};var result;try{result=reader.readRecords(this.data);}catch(e){this.fireEvent("loadexception",this,arg,null,e);callback.call(scope,null,arg,false);return;}
callback.call(scope,result,arg,true);},update:function(params,records){}});

Ext.data.HttpProxy=function(conn){Ext.data.HttpProxy.superclass.constructor.call(this);this.conn=conn.events?conn:new Ext.data.Connection(conn);};Ext.extend(Ext.data.HttpProxy,Ext.data.DataProxy,{getConnection:function(){return this.conn;},load:function(params,reader,callback,scope,arg){if(this.fireEvent("beforeload",this,params)!==false){this.conn.request({params:params||{},request:{callback:callback,scope:scope,arg:arg},reader:reader,callback:this.loadResponse,scope:this});}else{callback.call(scope||this,null,arg,false);}},loadResponse:function(o,success,response){if(!success){this.fireEvent("loadexception",this,o,response);o.request.callback.call(o.request.scope,null,o.request.arg,false);return;}
var result;try{result=o.reader.read(response);}catch(e){this.fireEvent("loadexception",this,o,response,e);o.request.callback.call(o.request.scope,null,o.request.arg,false);return;}
this.fireEvent("load",this,o,o.request.arg);o.request.callback.call(o.request.scope,result,o.request.arg,true);},update:function(dataSet){},updateResponse:function(dataSet){}});

Ext.data.ScriptTagProxy=function(config){Ext.data.ScriptTagProxy.superclass.constructor.call(this);Ext.apply(this,config);this.head=document.getElementsByTagName("head")[0];};Ext.data.ScriptTagProxy.TRANS_ID=1000;Ext.extend(Ext.data.ScriptTagProxy,Ext.data.DataProxy,{timeout:30000,callbackParam:"callback",nocache:true,load:function(params,reader,callback,scope,arg){if(this.fireEvent("beforeload",this,params)!==false){var p=Ext.urlEncode(Ext.apply(params,this.extraParams));var url=this.url;url+=(url.indexOf("?")!=-1?"&":"?")+p;if(this.nocache){url+="&_dc="+(new Date().getTime());}
var transId=++Ext.data.ScriptTagProxy.TRANS_ID;var trans={id:transId,cb:"stcCallback"+transId,scriptId:"stcScript"+transId,params:params,arg:arg,url:url,callback:callback,scope:scope,reader:reader};var conn=this;window[trans.cb]=function(o){conn.handleResponse(o,trans);};url+=String.format("&{0}={1}",this.callbackParam,trans.cb);if(this.autoAbort!==false){this.abort();}
trans.timeoutId=this.handleFailure.defer(this.timeout,this,[trans]);var script=document.createElement("script");script.setAttribute("src",url);script.setAttribute("type","text/javascript");script.setAttribute("id",trans.scriptId);this.head.appendChild(script);this.trans=trans;}else{callback.call(scope||this,null,arg,false);}},isLoading:function(){return this.trans?true:false;},abort:function(){if(this.isLoading()){this.destroyTrans(this.trans);}},destroyTrans:function(trans,isLoaded){this.head.removeChild(document.getElementById(trans.scriptId));clearTimeout(trans.timeoutId);if(isLoaded){window[trans.cb]=undefined;try{delete window[trans.cb];}catch(e){}}else{window[trans.cb]=function(){window[trans.cb]=undefined;try{delete window[trans.cb];}catch(e){}};}},handleResponse:function(o,trans){this.trans=false;this.destroyTrans(trans,true);var result;try{result=trans.reader.readRecords(o);}catch(e){this.fireEvent("loadexception",this,o,trans.arg,e);trans.callback.call(trans.scope||window,null,trans.arg,false);return;}
this.fireEvent("load",this,o,trans.arg);trans.callback.call(trans.scope||window,result,trans.arg,true);},handleFailure:function(trans){this.trans=false;this.destroyTrans(trans,false);this.fireEvent("loadexception",this,null,trans.arg);trans.callback.call(trans.scope||window,null,trans.arg,false);}});

Ext.data.JsonReader=function(meta,recordType){Ext.data.JsonReader.superclass.constructor.call(this,meta,recordType);};Ext.extend(Ext.data.JsonReader,Ext.data.DataReader,{read:function(response){var json=response.responseText;var o=eval("("+json+")");if(!o){throw{message:"JsonReader.read: Json object not found"};}
return this.readRecords(o);},simpleAccess:function(obj,subsc){return obj[subsc];},getJsonAccessor:function(){var re=/[\[\.]/;return function(expr){try{return(re.test(expr))?new Function("obj","return obj."+expr):function(obj){return obj[expr];};}catch(e){}
return Ext.emptyFn;};}(),readRecords:function(o){this.jsonData=o;var s=this.meta,Record=this.recordType,f=Record.prototype.fields,fi=f.items,fl=f.length;if(!this.ef){if(s.totalProperty){this.getTotal=this.getJsonAccessor(s.totalProperty);}
if(s.successProperty){this.getSuccess=this.getJsonAccessor(s.successProperty);}
this.getRoot=s.root?this.getJsonAccessor(s.root):function(p){return p;};if(s.id){var g=this.getJsonAccessor(s.id);this.getId=function(rec){var r=g(rec);return(r===undefined||r==="")?null:r;};}else{this.getId=function(){return null;};}
this.ef=[];for(var i=0;i<fl;i++){f=fi[i];var map=(f.mapping!==undefined&&f.mapping!==null)?f.mapping:f.name;this.ef[i]=this.getJsonAccessor(map);}}
var root=this.getRoot(o),c=root.length,totalRecords=c,success=true;if(s.totalProperty){var v=parseInt(this.getTotal(o),10);if(!isNaN(v)){totalRecords=v;}}
if(s.successProperty){var v=this.getSuccess(o);if(v===false||v==='false'){success=false;}}
var records=[];for(var i=0;i<c;i++){var n=root[i];var values={};var id=this.getId(n);for(var j=0;j<fl;j++){f=fi[j];var v=this.ef[j](n);values[f.name]=f.convert((v!==undefined)?v:f.defaultValue);}
var record=new Record(values,id);record.json=n;records[i]=record;}
return{success:success,records:records,totalRecords:totalRecords};}});

Ext.data.XmlReader=function(meta,recordType){Ext.data.XmlReader.superclass.constructor.call(this,meta,recordType);};Ext.extend(Ext.data.XmlReader,Ext.data.DataReader,{read:function(response){var doc=response.responseXML;if(!doc){throw{message:"XmlReader.read: XML Document not available"};}
return this.readRecords(doc);},readRecords:function(doc){this.xmlData=doc;var root=doc.documentElement||doc;var q=Ext.DomQuery;var recordType=this.recordType,fields=recordType.prototype.fields;var sid=this.meta.id;var totalRecords=0,success=true;if(this.meta.totalRecords){totalRecords=q.selectNumber(this.meta.totalRecords,root,0);}
if(this.meta.success){var sv=q.selectValue(this.meta.success,root,true);success=sv!==false&&sv!=='false';}
var records=[];var ns=q.select(this.meta.record,root);for(var i=0,len=ns.length;i<len;i++){var n=ns[i];var values={};var id=sid?q.selectValue(sid,n):undefined;for(var j=0,jlen=fields.length;j<jlen;j++){var f=fields.items[j];var v=q.selectValue(f.mapping||f.name,n,f.defaultValue);v=f.convert(v);values[f.name]=v;}
var record=new recordType(values,id);record.node=n;records[records.length]=record;}
return{success:success,records:records,totalRecords:totalRecords||records.length};}});

Ext.data.ArrayReader=function(meta,recordType){Ext.data.ArrayReader.superclass.constructor.call(this,meta,recordType);};Ext.extend(Ext.data.ArrayReader,Ext.data.JsonReader,{readRecords:function(o){var sid=this.meta?this.meta.id:null;var recordType=this.recordType,fields=recordType.prototype.fields;var records=[];var root=o;for(var i=0;i<root.length;i++){var n=root[i];var values={};var id=((sid||sid===0)&&n[sid]!==undefined&&n[sid]!==""?n[sid]:null);for(var j=0,jlen=fields.length;j<jlen;j++){var f=fields.items[j];var k=f.mapping!==undefined&&f.mapping!==null?f.mapping:j;var v=n[k]!==undefined?n[k]:f.defaultValue;v=f.convert(v);values[f.name]=v;}
var record=new recordType(values,id);record.json=n;records[records.length]=record;}
return{records:records,totalRecords:records.length};}});
