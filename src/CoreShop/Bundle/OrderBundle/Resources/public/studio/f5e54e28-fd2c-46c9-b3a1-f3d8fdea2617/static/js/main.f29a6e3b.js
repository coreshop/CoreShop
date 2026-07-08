/*! For license information please see main.f29a6e3b.js.LICENSE.txt */
(()=>{var __webpack_modules__={6953:function(e,t,r){Object.defineProperty(t,Symbol.toStringTag,{value:"Module"});let o=r(5636);t.logAndReport=function(e,t,r,n,i,a){return n(o.getShortErrorMsg(e,t,r,i))}},5748:function(e,t,r){let o=r(8686),n={[o.RUNTIME_001]:"Failed to get remoteEntry exports.",[o.RUNTIME_002]:'The remote entry interface does not contain "init"',[o.RUNTIME_003]:"Failed to get manifest.",[o.RUNTIME_004]:"Failed to locate remote.",[o.RUNTIME_005]:"Invalid loadShareSync function call from bundler runtime",[o.RUNTIME_006]:"Invalid loadShareSync function call from runtime",[o.RUNTIME_007]:"Failed to get remote snapshot.",[o.RUNTIME_008]:"Failed to load script resources.",[o.RUNTIME_009]:"Please call createInstance first.",[o.RUNTIME_010]:'The name option cannot be changed after initialization. If you want to create a new instance with a different name, please use "createInstance" api.',[o.RUNTIME_011]:"The remoteEntry URL is missing from the remote snapshot.",[o.RUNTIME_012]:'The getter for the shared module is not a function. This may be caused by setting "shared.import: false" without the host providing the corresponding lib.',[o.RUNTIME_013]:"The manifest is not a valid Module Federation manifest.",[o.RUNTIME_014]:"The remote does not expose the requested module.",[o.RUNTIME_015]:"Remote container initialization failed."},i={[o.TYPE_001]:"Failed to generate type declaration. Execute the below cmd to reproduce and fix the error."},a={[o.BUILD_001]:"Failed to find expose module.",[o.BUILD_002]:"PublicPath is required in prod mode."},s={...n,...i,...a};t.buildDescMap=a,t.errorDescMap=s,t.runtimeDescMap=n,t.typeDescMap=i},8686:function(e,t){let r="RUNTIME-001",o="RUNTIME-002",n="RUNTIME-003",i="RUNTIME-004",a="RUNTIME-005",s="RUNTIME-006",l="RUNTIME-007",c="RUNTIME-008",d="RUNTIME-009",u="RUNTIME-010",p="RUNTIME-011",f="RUNTIME-012",h="RUNTIME-013",m="RUNTIME-014",g="RUNTIME-015",y="TYPE-001",b="BUILD-002";t.BUILD_001="BUILD-001",t.BUILD_002=b,t.RUNTIME_001=r,t.RUNTIME_002=o,t.RUNTIME_003=n,t.RUNTIME_004=i,t.RUNTIME_005=a,t.RUNTIME_006=s,t.RUNTIME_007=l,t.RUNTIME_008=c,t.RUNTIME_009=d,t.RUNTIME_010=u,t.RUNTIME_011=p,t.RUNTIME_012=f,t.RUNTIME_013=h,t.RUNTIME_014=m,t.RUNTIME_015=g,t.TYPE_001=y},5636:function(e,t){let r=e=>`View the docs to see how to solve: https://module-federation.io/guide/troubleshooting/${e.split("-")[0].toLowerCase()}#${e.toLowerCase()}`;t.getShortErrorMsg=(e,t,o,n)=>{let i=[`${[t[e]]} #${e}`];return o&&i.push(`args: ${JSON.stringify(o)}`),i.push(r(e)),n&&i.push(`Original Error Message:
 ${n}`),i.join("\n")}},8933:function(e,t,r){Object.defineProperty(t,Symbol.toStringTag,{value:"Module"});let o=r(8686),n=r(5636),i=r(5748);t.BUILD_001=o.BUILD_001,t.BUILD_002=o.BUILD_002,t.RUNTIME_001=o.RUNTIME_001,t.RUNTIME_002=o.RUNTIME_002,t.RUNTIME_003=o.RUNTIME_003,t.RUNTIME_004=o.RUNTIME_004,t.RUNTIME_005=o.RUNTIME_005,t.RUNTIME_006=o.RUNTIME_006,t.RUNTIME_007=o.RUNTIME_007,t.RUNTIME_008=o.RUNTIME_008,t.RUNTIME_009=o.RUNTIME_009,t.RUNTIME_010=o.RUNTIME_010,t.RUNTIME_011=o.RUNTIME_011,t.RUNTIME_012=o.RUNTIME_012,t.RUNTIME_013=o.RUNTIME_013,t.RUNTIME_014=o.RUNTIME_014,t.RUNTIME_015=o.RUNTIME_015,t.TYPE_001=o.TYPE_001,t.buildDescMap=i.buildDescMap,t.errorDescMap=i.errorDescMap,t.getShortErrorMsg=n.getShortErrorMsg,t.runtimeDescMap=i.runtimeDescMap,t.typeDescMap=i.typeDescMap},6350:function(e,t){var r=Object.defineProperty;t.__exportAll=(e,t)=>{let o={};for(var n in e)r(o,n,{get:e[n],enumerable:!0});return t||r(o,Symbol.toStringTag,{value:"Module"}),o}},4696:function(e,t){let r="default";t.DEFAULT_REMOTE_TYPE="global",t.DEFAULT_SCOPE=r},6433:function(e,t,r){let o=r(9869),n=r(4696),i=r(5203),a=r(2911),s=r(3687),l=r(2726);r(2098);let c=r(3341),d=r(669),u=r(4514),p=r(2187),f=r(6537),h=r(6359);r(6623);let m=r(5018),g=r(3232),y=r(7986),b=r(994),E=r(8703),S=r(5652),x=r(8933);t.ModuleFederation=class{initOptions(e){e.name&&e.name!==this.options.name&&o.error((0,x.getShortErrorMsg)(x.RUNTIME_010,x.runtimeDescMap)),this.registerPlugins(e.plugins);let t=this.formatOptions(this.options,e);return this.options=t,t}async loadShare(e,t){return this.sharedHandler.loadShare(e,t)}loadShareSync(e,t){return this.sharedHandler.loadShareSync(e,t)}initializeSharing(){let e=arguments.length>0&&void 0!==arguments[0]?arguments[0]:n.DEFAULT_SCOPE,t=arguments.length>1?arguments[1]:void 0;return this.sharedHandler.initializeSharing(e,t)}initRawContainer(e,t,r){let o=l.getRemoteInfo({name:e,entry:t}),n=new c.Module({host:this,remoteInfo:o});return n.remoteEntryExports=r,this.moduleCache.set(e,n),n}async loadRemote(e,t){return this.remoteHandler.loadRemote(e,t)}async preloadRemote(e){return this.remoteHandler.preloadRemote(e)}initShareScopeMap(e,t){let r=arguments.length>2&&void 0!==arguments[2]?arguments[2]:{};this.sharedHandler.initShareScopeMap(e,t,r)}formatOptions(e,t){let{allShareInfos:r}=i.formatShareConfigs(e,t),{userOptions:o,options:n}=this.hooks.lifecycle.beforeInit.emit({origin:this,userOptions:t,options:e,shareInfo:r}),a=this.remoteHandler.formatAndRegisterRemote(n,o),{allShareInfos:s}=this.sharedHandler.registerShared(n,o),l=[...n.plugins];o.plugins&&o.plugins.forEach(e=>{l.includes(e)||l.push(e)});let c={...e,...t,plugins:l,remotes:a,shared:s,id:o.id||e.id};return this.hooks.lifecycle.init.emit({origin:this,options:c}),c}registerPlugins(e){let t=s.registerPlugins(e,this);this.options.plugins=this.options.plugins.reduce((e,t)=>(t&&e&&!e.find(e=>e.name===t.name)&&e.push(t),e),t||[])}registerRemotes(e,t){return this.remoteHandler.registerRemotes(e,t)}registerShared(e){this.sharedHandler.registerShared(this.options,{...this.options,shared:e})}constructor(e){this.hooks=new h.PluginSystem({beforeInit:new p.SyncWaterfallHook("beforeInit"),init:new d.SyncHook,beforeInitContainer:new f.AsyncWaterfallHook("beforeInitContainer"),initContainer:new f.AsyncWaterfallHook("initContainer")}),this.version="2.5.1",this.moduleCache=new Map,this.loaderHook=new h.PluginSystem({getModuleInfo:new d.SyncHook,createScript:new d.SyncHook,createLink:new d.SyncHook,fetch:new u.AsyncHook,loadEntryError:new u.AsyncHook,afterLoadEntry:new u.AsyncHook("afterLoadEntry"),beforeInitRemote:new u.AsyncHook("beforeInitRemote"),afterInitRemote:new u.AsyncHook("afterInitRemote"),beforeGetExpose:new u.AsyncHook("beforeGetExpose"),afterGetExpose:new u.AsyncHook("afterGetExpose"),beforeExecuteFactory:new u.AsyncHook("beforeExecuteFactory"),afterExecuteFactory:new u.AsyncHook("afterExecuteFactory"),getModuleFactory:new u.AsyncHook}),this.bridgeHook=new h.PluginSystem({beforeBridgeRender:new d.SyncHook,afterBridgeRender:new d.SyncHook,beforeBridgeDestroy:new d.SyncHook,afterBridgeDestroy:new d.SyncHook});const t=[m.snapshotPlugin(),g.generatePreloadAssetsPlugin()],r={id:a.getBuilderId(),name:e.name,plugins:t,remotes:[],shared:{},inBrowser:S.isBrowserEnvValue};this.name=e.name,this.options=r,this.snapshotHandler=new y.SnapshotHandler(this),this.sharedHandler=new b.SharedHandler(this),this.remoteHandler=new E.RemoteHandler(this),this.shareScopeMap=this.sharedHandler.shareScopeMap,this.registerPlugins([...r.plugins,...e.plugins||[]]),this.options=this.formatOptions(r,e)}}},201:function(e,t,r){let o=r(9869),n=r(8844),i=r(5652),a="object"==typeof globalThis?globalThis:window,s=(()=>{try{return document.defaultView}catch{return a}})(),l=s;function c(e,t,r){Object.defineProperty(e,t,{value:r,configurable:!1,writable:!0})}function d(e,t){return Object.hasOwnProperty.call(e,t)}d(a,"__GLOBAL_LOADING_REMOTE_ENTRY__")||c(a,"__GLOBAL_LOADING_REMOTE_ENTRY__",{});let u=a.__GLOBAL_LOADING_REMOTE_ENTRY__;function p(e){var t,r,o,n,i,a;d(e,"__VMOK__")&&!d(e,"__FEDERATION__")&&c(e,"__FEDERATION__",e.__VMOK__),d(e,"__FEDERATION__")||(c(e,"__FEDERATION__",{__GLOBAL_PLUGIN__:[],__INSTANCES__:[],moduleInfo:{},__SHARE__:{},__MANIFEST_LOADING__:{},__PRELOADED_MAP__:new Map}),c(e,"__VMOK__",e.__FEDERATION__)),(t=e.__FEDERATION__).__GLOBAL_PLUGIN__??(t.__GLOBAL_PLUGIN__=[]),(r=e.__FEDERATION__).__INSTANCES__??(r.__INSTANCES__=[]),(o=e.__FEDERATION__).moduleInfo??(o.moduleInfo={}),(n=e.__FEDERATION__).__SHARE__??(n.__SHARE__={}),(i=e.__FEDERATION__).__MANIFEST_LOADING__??(i.__MANIFEST_LOADING__={}),(a=e.__FEDERATION__).__PRELOADED_MAP__??(a.__PRELOADED_MAP__=new Map)}function f(){a.__FEDERATION__.__GLOBAL_PLUGIN__=[],a.__FEDERATION__.__INSTANCES__=[],a.__FEDERATION__.moduleInfo={},a.__FEDERATION__.__SHARE__={},a.__FEDERATION__.__MANIFEST_LOADING__={},Object.keys(u).forEach(e=>{delete u[e]})}function h(e){a.__FEDERATION__.__INSTANCES__.push(e)}function m(){return a.__FEDERATION__.__DEBUG_CONSTRUCTOR__}function g(e){let t=arguments.length>1&&void 0!==arguments[1]?arguments[1]:(0,i.isDebugMode)();t&&(a.__FEDERATION__.__DEBUG_CONSTRUCTOR__=e,a.__FEDERATION__.__DEBUG_CONSTRUCTOR_VERSION__="2.5.1")}function y(e,t){if("string"==typeof t)if(e[t])return{value:e[t],key:t};else{for(let r of Object.keys(e)){let[o,n]=r.split(":"),i=`${o}:${t}`,a=e[i];if(a)return{value:a,key:i}}return{value:void 0,key:t}}o.error(`getInfoWithoutType: "key" must be a string, got ${typeof t} (${JSON.stringify(t)}).`)}p(a),p(s);let b=()=>s.__FEDERATION__.moduleInfo,E=(e,t)=>{let r=y(t,n.getFMId(e)).value;if(r&&!r.version&&"version"in e&&e.version&&(r.version=e.version),r)return r;if("version"in e&&e.version){let{version:t,...r}=e,o=n.getFMId(r),i=y(s.__FEDERATION__.moduleInfo,o).value;if((null==i?void 0:i.version)===t)return i}},S=e=>E(e,s.__FEDERATION__.moduleInfo),x=(e,t)=>{let r=n.getFMId(e);return s.__FEDERATION__.moduleInfo[r]=t,s.__FEDERATION__.moduleInfo},_=e=>(s.__FEDERATION__.moduleInfo={...s.__FEDERATION__.moduleInfo,...e},()=>{for(let t of Object.keys(e))delete s.__FEDERATION__.moduleInfo[t]}),v=(e,t)=>{let r=t||`__FEDERATION_${e}:custom__`;return{remoteEntryKey:r,entryExports:a[r]}},R=e=>{let{__GLOBAL_PLUGIN__:t}=s.__FEDERATION__;e.forEach(e=>{-1===t.findIndex(t=>t.name===e.name)?t.push(e):o.warn(`The plugin ${e.name} has been registered.`)})},I=()=>s.__FEDERATION__.__GLOBAL_PLUGIN__,T=e=>a.__FEDERATION__.__PRELOADED_MAP__.get(e),w=e=>a.__FEDERATION__.__PRELOADED_MAP__.set(e,!0);t.CurrentGlobal=a,t.Global=l,t.addGlobalSnapshot=_,t.getGlobalFederationConstructor=m,t.getGlobalHostPlugins=I,t.getGlobalSnapshot=b,t.getGlobalSnapshotInfoByModuleInfo=S,t.getInfoWithoutType=y,t.getPreloaded=T,t.getRemoteEntryExports=v,t.getTargetSnapshotInfoByModuleInfo=E,t.globalLoading=u,t.nativeGlobal=s,t.registerGlobalPlugins=R,t.resetFederationGlobalInfo=f,t.setGlobalFederationConstructor=g,t.setGlobalFederationInstance=h,t.setGlobalSnapshotInfoByModuleInfo=x,t.setPreloaded=w},4223:function(e,t,r){let o=r(201),n=r(5203),i=r(5241),a=r(2726);r(2098);let s=r(6241),l={getRegisteredShare:n.getRegisteredShare,getGlobalShareScope:n.getGlobalShareScope};t.default={global:{Global:o.Global,nativeGlobal:o.nativeGlobal,resetFederationGlobalInfo:o.resetFederationGlobalInfo,setGlobalFederationInstance:o.setGlobalFederationInstance,getGlobalFederationConstructor:o.getGlobalFederationConstructor,setGlobalFederationConstructor:o.setGlobalFederationConstructor,getInfoWithoutType:o.getInfoWithoutType,getGlobalSnapshot:o.getGlobalSnapshot,getTargetSnapshotInfoByModuleInfo:o.getTargetSnapshotInfoByModuleInfo,getGlobalSnapshotInfoByModuleInfo:o.getGlobalSnapshotInfoByModuleInfo,setGlobalSnapshotInfoByModuleInfo:o.setGlobalSnapshotInfoByModuleInfo,addGlobalSnapshot:o.addGlobalSnapshot,getRemoteEntryExports:o.getRemoteEntryExports,registerGlobalPlugins:o.registerGlobalPlugins,getGlobalHostPlugins:o.getGlobalHostPlugins,getPreloaded:o.getPreloaded,setPreloaded:o.setPreloaded},share:l,utils:{matchRemoteWithNameAndExpose:i.matchRemoteWithNameAndExpose,preloadAssets:s.preloadAssets,getRemoteInfo:a.getRemoteInfo}}},4328:function(e,t,r){Object.defineProperty(t,Symbol.toStringTag,{value:"Module"});let o=r(9869),n=r(8844),i=r(201),a=r(3743),s=r(5203),l=r(5241),c=r(2726);r(2098);let d=r(4223),u=r(3341),p=r(6433),f=r(8957),h=r(5652),m=d.default;t.CurrentGlobal=i.CurrentGlobal,t.Global=i.Global,t.Module=u.Module,t.ModuleFederation=p.ModuleFederation,t.addGlobalSnapshot=i.addGlobalSnapshot,t.assert=o.assert,t.error=o.error,t.getGlobalFederationConstructor=i.getGlobalFederationConstructor,t.getGlobalSnapshot=i.getGlobalSnapshot,t.getInfoWithoutType=i.getInfoWithoutType,t.getRegisteredShare=s.getRegisteredShare,t.getRemoteEntry=c.getRemoteEntry,t.getRemoteInfo=c.getRemoteInfo,t.helpers=m,t.isStaticResourcesEqual=n.isStaticResourcesEqual,Object.defineProperty(t,"loadScript",{enumerable:!0,get:function(){return h.loadScript}}),Object.defineProperty(t,"loadScriptNode",{enumerable:!0,get:function(){return h.loadScriptNode}}),t.matchRemoteWithNameAndExpose=l.matchRemoteWithNameAndExpose,t.registerGlobalPlugins=i.registerGlobalPlugins,t.resetFederationGlobalInfo=i.resetFederationGlobalInfo,t.safeWrapper=n.safeWrapper,t.satisfy=a.satisfy,t.setGlobalFederationConstructor=i.setGlobalFederationConstructor,t.setGlobalFederationInstance=i.setGlobalFederationInstance,Object.defineProperty(t,"types",{enumerable:!0,get:function(){return f.type_exports}})},3341:function(e,t,r){let o=r(9869),n=r(8844),i=r(5241),a=r(2726),s=r(1607);r(2098);let l=r(5652),c=r(8933);function d(e){if(!e||!("modules"in e)||!Array.isArray(e.modules))return;let t=e.modules.map(e=>e.moduleName).filter(Boolean);return t.length?t.join(","):void 0}function u(e,t,r){let o=t,n=Array.isArray(e.shareScope)?e.shareScope:[e.shareScope];n.length||n.push("default"),n.forEach(e=>{o[e]||(o[e]={})});let i={version:e.version||"",shareScopeKeys:Array.isArray(e.shareScope)?n:e.shareScope||"default"};return Object.defineProperty(i,"shareScopeMap",{value:o,enumerable:!1}),{remoteEntryInitOptions:i,shareScope:o[n[0]],initScope:r??[]}}t.Module=class{async getEntry(e){if(this.remoteEntryExports)return this.remoteEntryExports;let t=await a.getRemoteEntry({origin:this.host,remoteInfo:this.remoteInfo,remoteEntryExports:this.remoteEntryExports,resourceContext:{initiator:"loadRemote",id:i.composeRemoteRequestId(this.remoteInfo.name,e),resourceType:"remoteEntry"}});return o.assert(t,`remoteEntryExports is undefined 
 ${(0,l.safeToString)(this.remoteInfo)}`),this.remoteEntryExports=t,this.remoteEntryExports}async init(e,t,r,n){let i=await this.getEntry(n);if(this.inited)return await this.host.loaderHook.lifecycle.afterInitRemote.emit({id:e,remoteInfo:this.remoteInfo,remoteSnapshot:t,remoteEntryExports:i,cached:!0,origin:this.host}),i;if(this.initPromise){try{await this.initPromise,await this.host.loaderHook.lifecycle.afterInitRemote.emit({id:e,remoteInfo:this.remoteInfo,remoteSnapshot:t,remoteEntryExports:i,cached:!0,origin:this.host})}catch(r){throw await this.host.loaderHook.lifecycle.afterInitRemote.emit({id:e,remoteInfo:this.remoteInfo,remoteSnapshot:t,remoteEntryExports:i,error:r,cached:!0,origin:this.host}),r}return i}this.initing=!0,this.initPromise=(async()=>{await this.host.loaderHook.lifecycle.beforeInitRemote.emit({id:e,remoteInfo:this.remoteInfo,remoteSnapshot:t,origin:this.host});let{remoteEntryInitOptions:n,shareScope:a,initScope:l}=u(this.remoteInfo,this.host.shareScopeMap,r),d=await this.host.hooks.lifecycle.beforeInitContainer.emit({shareScope:a,remoteEntryInitOptions:n,initScope:l,remoteInfo:this.remoteInfo,origin:this.host});void 0===(null==i?void 0:i.init)&&o.error(c.RUNTIME_002,c.runtimeDescMap,{hostName:this.host.name,remoteName:this.remoteInfo.name,remoteEntryUrl:this.remoteInfo.entry,remoteEntryKey:this.remoteInfo.entryGlobalName},void 0,s.optionsToMFContext(this.host.options));try{await i.init(d.shareScope,d.initScope,d.remoteEntryInitOptions)}catch(e){o.error(c.RUNTIME_015,c.runtimeDescMap,{hostName:this.host.name,remoteName:this.remoteInfo.name,remoteEntryUrl:this.remoteInfo.entry,remoteEntryKey:this.remoteInfo.entryGlobalName,shareScope:this.remoteInfo.shareScope},`${e}`,s.optionsToMFContext(this.host.options))}await this.host.hooks.lifecycle.initContainer.emit({...d,id:e,remoteSnapshot:t,remoteEntryExports:i}),this.inited=!0})();try{await this.initPromise,await this.host.loaderHook.lifecycle.afterInitRemote.emit({id:e,remoteInfo:this.remoteInfo,remoteSnapshot:t,remoteEntryExports:i,origin:this.host})}catch(r){throw await this.host.loaderHook.lifecycle.afterInitRemote.emit({id:e,remoteInfo:this.remoteInfo,remoteSnapshot:t,remoteEntryExports:i,error:r,origin:this.host}),r}finally{this.initing=!1,this.initPromise=void 0}return i}async get(e,t,r,i){let a,{loadFactory:l=!0}=r||{loadFactory:!0},u=await this.init(e,i,void 0,t);this.lib=u,await this.host.loaderHook.lifecycle.beforeGetExpose.emit({id:e,expose:t,moduleInfo:this.remoteInfo,remoteEntryExports:u,origin:this.host});try{let r=await this.host.loaderHook.lifecycle.getModuleFactory.emit({remoteEntryExports:u,expose:t,moduleInfo:this.remoteInfo});(a="function"==typeof r?r:void 0)||(a=await u.get(t)),a||o.error(c.RUNTIME_014,c.runtimeDescMap,{hostName:this.host.name,remoteName:this.remoteInfo.name,remoteEntryUrl:this.remoteInfo.entry,expose:t,requestId:e,availableExposes:d(i)},void 0,s.optionsToMFContext(this.host.options)),await this.host.loaderHook.lifecycle.afterGetExpose.emit({id:e,expose:t,moduleInfo:this.remoteInfo,remoteEntryExports:u,moduleFactory:a,origin:this.host})}catch(r){throw await this.host.loaderHook.lifecycle.afterGetExpose.emit({id:e,expose:t,moduleInfo:this.remoteInfo,remoteEntryExports:u,error:r,origin:this.host}),r}let p=n.processModuleAlias(this.remoteInfo.name,t),f=this.wraperFactory(a,p);if(!l)return f;await this.host.loaderHook.lifecycle.beforeExecuteFactory.emit({id:e,expose:t,moduleInfo:this.remoteInfo,loadFactory:l,origin:this.host});try{let r=await f();return await this.host.loaderHook.lifecycle.afterExecuteFactory.emit({id:e,expose:t,moduleInfo:this.remoteInfo,loadFactory:l,exposeModule:r,origin:this.host}),r}catch(r){throw await this.host.loaderHook.lifecycle.afterExecuteFactory.emit({id:e,expose:t,moduleInfo:this.remoteInfo,loadFactory:l,error:r,origin:this.host}),r}}wraperFactory(e,t){function r(e,t){e&&"object"==typeof e&&Object.isExtensible(e)&&!Object.getOwnPropertyDescriptor(e,Symbol.for("mf_module_id"))&&Object.defineProperty(e,Symbol.for("mf_module_id"),{value:t,enumerable:!1})}return()=>{let o=e();return o instanceof Promise?o.then(e=>(r(e,t),e)):(r(o,t),o)}}constructor({remoteInfo:e,host:t}){this.inited=!1,this.initing=!1,this.lib=void 0,this.remoteInfo=e,this.host=t}}},3232:function(e,t,r){let o=r(8844),n=r(201),i=r(5203);r(2098);let a=r(6241),s=r(5018),l=r(5652);function c(e){let t=e.split(":");return 1===t.length?{name:t[0],version:void 0}:2===t.length?{name:t[0],version:t[1]}:{name:t[1],version:t[2]}}function d(e,t,r,i){let a=arguments.length>4&&void 0!==arguments[4]?arguments[4]:{},s=arguments.length>5?arguments[5]:void 0,{value:u}=n.getInfoWithoutType(e,o.getFMId(t)),p=s||u;if(p&&!(0,l.isManifestProvider)(p)&&(r(p,t,i),p.remotesInfo))for(let t of Object.keys(p.remotesInfo)){if(a[t])continue;a[t]=!0;let o=c(t),n=p.remotesInfo[t];d(e,{name:o.name,version:n.matchedVersion},r,!1,a,void 0)}}let u=(e,t)=>document.querySelector(`${e}[${"link"===e?"href":"src"}="${t}"]`);function p(e,t,r,s,c){let p=[],f=[],h=[],m=new Set,g=new Set,{options:y}=e,{preloadConfig:b}=t,{depsRemote:E}=b;if(d(s,r,(t,r,i)=>{var s;let c;if(i)c=b;else if(Array.isArray(E)){let e=E.find(e=>e.nameOrAlias===r.name||e.nameOrAlias===r.alias);if(!e)return;c=a.defaultPreloadArgs(e)}else{if(!0!==E)return;c=b}let d=(0,l.getResourceUrl)(t,o.getRemoteEntryInfoFromSnapshot(t).url);d&&h.push({name:r.name,moduleInfo:{name:r.name,entry:d,type:"remoteEntryType"in t?t.remoteEntryType:"global",entryGlobalName:"globalName"in t?t.globalName:r.name,shareScope:"",version:"version"in t?t.version:void 0},url:d});let u="modules"in t?t.modules:[],m=a.normalizePreloadExposes(c.exposes);function g(e){let r=e.map(e=>(0,l.getResourceUrl)(t,e));return c.filter?r.filter(c.filter):r}if(m.length&&"modules"in t&&(u=null==t||null==(s=t.modules)?void 0:s.reduce((e,t)=>((null==m?void 0:m.indexOf(t.moduleName))!==-1&&e.push(t),e),[])),u){let o=u.length;for(let i=0;i<o;i++){let o=u[i],a=`${r.name}/${o.moduleName}`;e.remoteHandler.hooks.lifecycle.handlePreloadModule.emit({id:"."===o.moduleName?r.name:a,name:r.name,remoteSnapshot:t,preloadConfig:c,remote:r,origin:e}),n.getPreloaded(a)||("all"===c.resourceCategory?(p.push(...g(o.assets.css.async)),p.push(...g(o.assets.css.sync)),f.push(...g(o.assets.js.async)),f.push(...g(o.assets.js.sync))):"sync"===c.resourceCategory&&(p.push(...g(o.assets.css.sync)),f.push(...g(o.assets.js.sync))),n.setPreloaded(a))}}},!0,{},c),c.shared&&c.shared.length>0){let t=(t,r)=>{let{shared:o}=i.getRegisteredShare(e.shareScopeMap,r.sharedName,t,e.sharedHandler.hooks.lifecycle.resolveShare)||{};o&&"function"==typeof o.lib&&(r.assets.js.sync.forEach(e=>{m.add(e)}),r.assets.css.sync.forEach(e=>{g.add(e)}))};c.shared.forEach(e=>{var r;let n=null==(r=y.shared)?void 0:r[e.sharedName];if(!n)return;let i=e.version?n.find(t=>t.version===e.version):n;i&&o.arrayOptions(i).forEach(r=>{t(r,e)})})}let S=f.filter(e=>!m.has(e)&&!u("script",e));return{cssAssets:p.filter(e=>!g.has(e)&&!u("link",e)),jsAssetsWithoutEntry:S,entryAssets:h.filter(e=>!u("script",e.url))}}t.generatePreloadAssetsPlugin=function(){return{name:"generate-preload-assets-plugin",async generatePreloadAssets(e){let{origin:t,preloadOptions:r,remoteInfo:n,remote:i,globalSnapshot:a,remoteSnapshot:c}=e;return l.isBrowserEnvValue?o.isRemoteInfoWithEntry(i)&&o.isPureRemoteEntry(i)?{cssAssets:[],jsAssetsWithoutEntry:[],entryAssets:[{name:i.name,url:i.entry,moduleInfo:{name:n.name,entry:i.entry,type:n.type||"global",entryGlobalName:"",shareScope:""}}]}:(s.assignRemoteInfo(n,c),p(t,r,n,a,c)):{cssAssets:[],jsAssetsWithoutEntry:[],entryAssets:[]}}}}},7986:function(e,t,r){let o=r(9869),n=r(8844),i=r(201),a=r(2726),s=r(1607);r(2098);let l=r(4514),c=r(6537),d=r(6359);r(6623);let u=r(5652),p=r(8933);function f(e,t){let r=i.getGlobalSnapshotInfoByModuleInfo({name:t.name,version:t.options.version}),o=r&&"remotesInfo"in r&&r.remotesInfo&&i.getInfoWithoutType(r.remotesInfo,e.name).value;return o&&o.matchedVersion?{hostGlobalSnapshot:r,globalSnapshot:i.getGlobalSnapshot(),remoteSnapshot:i.getGlobalSnapshotInfoByModuleInfo({name:e.name,version:o.matchedVersion})}:{hostGlobalSnapshot:void 0,globalSnapshot:i.getGlobalSnapshot(),remoteSnapshot:i.getGlobalSnapshotInfoByModuleInfo({name:e.name,version:"version"in e?e.version:void 0})}}t.SnapshotHandler=class{async loadRemoteSnapshotInfo(e){let t,r,{moduleInfo:a,id:l,initiator:c="loadRemote"}=e,{options:d}=this.HostInstance;await this.hooks.lifecycle.beforeLoadRemoteSnapshot.emit({options:d,moduleInfo:a,origin:this.HostInstance});let f=i.getGlobalSnapshotInfoByModuleInfo({name:this.HostInstance.options.name,version:this.HostInstance.options.version});f||(f={version:this.HostInstance.options.version||"",remoteEntry:"",remotesInfo:{}},i.addGlobalSnapshot({[this.HostInstance.options.name]:f})),f&&"remotesInfo"in f&&!i.getInfoWithoutType(f.remotesInfo,a.name).value&&("version"in a||"entry"in a)&&(f.remotesInfo={...null==f?void 0:f.remotesInfo,[a.name]:{matchedVersion:"version"in a?a.version:a.entry}});let{hostGlobalSnapshot:h,remoteSnapshot:m,globalSnapshot:g}=this.getGlobalRemoteInfo(a),{remoteSnapshot:y,globalSnapshot:b}=await this.hooks.lifecycle.loadSnapshot.emit({options:d,moduleInfo:a,hostGlobalSnapshot:h,remoteSnapshot:m,globalSnapshot:g});if(y)if((0,u.isManifestProvider)(y)){let e=u.isBrowserEnvValue?y.remoteEntry:y.ssrRemoteEntry||y.remoteEntry||"",o=await this.loadManifestSnapshot(e,a,{},{initiator:c,id:l||a.name}),n=i.setGlobalSnapshotInfoByModuleInfo({...a,entry:e},o);t=o,r=n}else{let{remoteSnapshot:e}=await this.hooks.lifecycle.loadRemoteSnapshot.emit({options:this.HostInstance.options,moduleInfo:a,remoteSnapshot:y,from:"global"});t=e,r=b}else if(n.isRemoteInfoWithEntry(a)){let e=await this.loadManifestSnapshot(a.entry,a,{},{initiator:c,id:l||a.name}),o=i.setGlobalSnapshotInfoByModuleInfo(a,e);t=e,r=o}else o.error(p.RUNTIME_007,p.runtimeDescMap,{remoteName:a.name,remoteVersion:a.version,hostName:this.HostInstance.options.name,globalSnapshot:JSON.stringify(b)},void 0,s.optionsToMFContext(this.HostInstance.options));return await this.hooks.lifecycle.afterLoadSnapshot.emit({id:l,host:this.HostInstance,options:d,moduleInfo:a,remoteSnapshot:t}),{remoteSnapshot:t,globalSnapshot:r}}getGlobalRemoteInfo(e){return f(e,this.HostInstance)}async getManifestJson(e,t,r,n){return(async()=>{let r=a.getRemoteInfo(t),i=this.manifestCache.get(e);if(i)return i;try{let t=await this.loaderHook.lifecycle.fetch.emit(e,{},r,n?{...n,url:e,resourceType:"manifest"}:void 0);t&&t instanceof Response||(t=await fetch(e,{})),i=await t.json()}catch(n){(i=await this.HostInstance.remoteHandler.hooks.lifecycle.errorLoadRemote.emit({id:e,error:n,from:"runtime",lifecycle:"afterResolve",remote:r,origin:this.HostInstance}))||(delete this.manifestLoading[e],o.error(p.RUNTIME_003,p.runtimeDescMap,{manifestUrl:e,moduleName:t.name,hostName:this.HostInstance.options.name},`${n}`,s.optionsToMFContext(this.HostInstance.options)))}let l=[!i.metaData&&"metaData",!i.exposes&&"exposes",!i.shared&&"shared"].filter(Boolean);return l.length>0&&await this.HostInstance.remoteHandler.hooks.lifecycle.errorLoadRemote.emit({id:e,error:Error(`"${e}" is not a valid federation manifest for remote "${t.name}". Missing required fields: ${l.join(", ")}.`),from:"runtime",lifecycle:"afterResolve",remote:r,origin:this.HostInstance}),l.length>0&&o.error(p.RUNTIME_013,p.runtimeDescMap,{manifestUrl:e,moduleName:t.name,hostName:this.HostInstance.options.name,missingFields:l.join(",")},void 0,s.optionsToMFContext(this.HostInstance.options)),this.manifestCache.set(e,i),i})()}async loadManifestSnapshot(e,t,r,o){let n=async()=>{let n=await this.getManifestJson(e,t,r,o),i=(0,u.generateSnapshotFromManifest)(n,{version:e}),{remoteSnapshot:a}=await this.hooks.lifecycle.loadRemoteSnapshot.emit({options:this.HostInstance.options,moduleInfo:t,manifestJson:n,remoteSnapshot:i,manifestUrl:e,from:"manifest"});return a};return this.manifestLoading[e]||(this.manifestLoading[e]=n().then(e=>e)),this.manifestLoading[e]}constructor(e){this.loadingHostSnapshot=null,this.manifestCache=new Map,this.hooks=new d.PluginSystem({beforeLoadRemoteSnapshot:new l.AsyncHook("beforeLoadRemoteSnapshot"),loadSnapshot:new c.AsyncWaterfallHook("loadGlobalSnapshot"),loadRemoteSnapshot:new c.AsyncWaterfallHook("loadRemoteSnapshot"),afterLoadSnapshot:new c.AsyncWaterfallHook("afterLoadSnapshot")}),this.manifestLoading=i.Global.__FEDERATION__.__MANIFEST_LOADING__,this.HostInstance=e,this.loaderHook=e.loaderHook}},t.getGlobalRemoteInfo=f},5018:function(e,t,r){let o=r(9869),n=r(8844),i=r(5241);r(2098);let a=r(6241),s=r(5652),l=r(8933);function c(e,t){let r=n.getRemoteEntryInfoFromSnapshot(t);r.url||o.error(l.RUNTIME_011,l.runtimeDescMap,{remoteName:e.name});let i=(0,s.getResourceUrl)(t,r.url);s.isBrowserEnvValue||i.startsWith("http")||(i=`https:${i}`),e.type=r.type,e.entryGlobalName=r.globalName,e.entry=i,e.version=t.version,e.buildVersion=t.buildVersion}function d(){return{name:"snapshot-plugin",async afterResolve(e){let{remote:t,pkgNameOrAlias:r,expose:o,origin:s,remoteInfo:l,id:d}=e;if(!n.isRemoteInfoWithEntry(t)||!n.isPureRemoteEntry(t)){let{remoteSnapshot:n,globalSnapshot:u}=await s.snapshotHandler.loadRemoteSnapshotInfo({moduleInfo:t,id:i.composeRemoteRequestId(t.name,o)});c(l,n);let p={remote:t,preloadConfig:{nameOrAlias:r,exposes:[o],resourceCategory:"sync",share:!1,depsRemote:!1}},f=await s.remoteHandler.hooks.lifecycle.generatePreloadAssets.emit({origin:s,preloadOptions:p,remoteInfo:l,remote:t,remoteSnapshot:n,globalSnapshot:u});return f&&a.preloadAssets(l,s,f,!1,{initiator:"loadRemote",id:d}).catch(()=>void 0),{...e,remoteSnapshot:n}}return e}}}t.assignRemoteInfo=c,t.snapshotPlugin=d},8703:function(e,t,r){let o=r(9869),n=r(201),i=r(4696),a=r(5203),s=r(5241),l=r(2726),c=r(1607);r(2098);let d=r(6241),u=r(3341),p=r(669),f=r(4514),h=r(2187),m=r(6537),g=r(6359);r(6623);let y=r(7986),b=r(5652),E=r(8933);t.RemoteHandler=class{formatAndRegisterRemote(e,t){return(t.remotes||[]).reduce((e,t)=>(this.registerRemote(t,e,{force:!1}),e),e.remotes)}setIdToRemoteMap(e,t){let{remote:r,expose:o}=t,{name:n,alias:i}=r;if(this.idToRemoteMap[e]={name:r.name,expose:o},i&&e.startsWith(n)){let t=e.replace(n,i);this.idToRemoteMap[t]={name:r.name,expose:o};return}if(i&&e.startsWith(i)){let t=e.replace(i,n);this.idToRemoteMap[t]={name:r.name,expose:o}}}async loadRemote(e,t){let r,{host:o}=this,n=s.matchRemoteWithNameAndExpose(o.options.remotes,e),i=e,a=null==n?void 0:n.expose,c=n?l.getRemoteInfo(n.remote):void 0;try{let{loadFactory:n=!0}=t||{loadFactory:!0},{module:s,moduleOptions:d,remoteMatchInfo:u}=await this.getRemoteModuleAndOptions({id:e}),{pkgNameOrAlias:p,remote:f,expose:h,id:m,remoteSnapshot:g}=u;i=m,a=h,c=l.getRemoteInfo(f);let y=await s.get(m,h,t,g),b=await this.hooks.lifecycle.onLoad.emit({id:m,pkgNameOrAlias:p,expose:h,exposeModule:n?y:void 0,exposeModuleFactory:n?void 0:y,remote:f,options:d,moduleInstance:s,origin:o});if(this.setIdToRemoteMap(e,u),r={id:i,expose:a,remote:c,options:t,origin:o},"function"==typeof b)return b;return y}catch(l){let n,{from:s="runtime"}=t||{from:"runtime"};try{n=await this.hooks.lifecycle.errorLoadRemote.emit({id:e,error:l,from:s,lifecycle:"onLoad",expose:a,remote:c,origin:o})}catch(e){throw r={id:i,expose:a,remote:c,options:t,error:e,origin:o},e}if(!n)throw r={id:i,expose:a,remote:c,options:t,error:l,origin:o},l;return r={id:i,expose:a,remote:c,options:t,error:l,origin:o,recovered:!0},n}finally{r&&await this.hooks.lifecycle.afterLoadRemote.emit(r)}}async preloadRemote(e){let t,{host:r}=this,o=[];await this.hooks.lifecycle.beforePreloadRemote.emit({preloadOps:e,options:r.options,origin:r});let n=d.formatPreloadArgs(r.options.remotes,e),i=e=>{let{preloadConfig:t,remote:r}=e,o=t.exposes||[];return o.length?o.map(o=>({ops:{...e,preloadConfig:{...t,exposes:[o]}},id:s.composeRemoteRequestId(r.name,o)})):[{ops:e,id:`${r.name}/*`}]};await Promise.all(n.flatMap(i).map(async e=>{let{ops:t,id:n}=e,{remote:i,preloadConfig:a}=t,s=l.getRemoteInfo(i);try{let{globalSnapshot:e,remoteSnapshot:l}=await r.snapshotHandler.loadRemoteSnapshotInfo({moduleInfo:i,id:n,initiator:"preloadRemote"}),c=await this.hooks.lifecycle.generatePreloadAssets.emit({origin:r,preloadOptions:t,remote:i,remoteInfo:s,globalSnapshot:e,remoteSnapshot:l});if(!c)return;let u=await d.preloadAssets(s,r,c,!0,{initiator:"preloadRemote",id:n});o.push({remote:i,remoteInfo:s,preloadConfig:a,id:n,results:u})}catch(e){o.push({remote:i,remoteInfo:s,preloadConfig:a,id:n,results:[{url:s.entry,status:"error",resourceType:/\.json(?:$|[?#])/i.test(s.entry)?"manifest":"remoteEntry",initiator:"preloadRemote",id:n,error:e}]})}}));let a=o.flatMap(e=>e.results.filter(e=>"error"===e.status||"timeout"===e.status));if(a.length>0&&Object.assign(t=Error(`preloadRemote failed to load ${a.length} resource(s).`),{results:o,failedResults:a}),await this.hooks.lifecycle.afterPreloadRemote.emit({preloadOps:e,options:r.options,origin:r,results:o,error:t}),t)throw t}registerRemotes(e,t){let{host:r}=this;e.forEach(e=>{this.registerRemote(e,r.options.remotes,{force:null==t?void 0:t.force})})}async getRemoteModuleAndOptions(e){let t,{host:r}=this,{id:n}=e;try{t=await this.hooks.lifecycle.beforeRequest.emit({id:n,options:r.options,origin:r})}catch(e){if(!(t=await this.hooks.lifecycle.errorLoadRemote.emit({id:n,options:r.options,origin:r,from:"runtime",error:e,lifecycle:"beforeRequest"})))throw e}let{id:i}=t,a=s.matchRemoteWithNameAndExpose(r.options.remotes,i);if(!a)try{o.error(E.RUNTIME_004,E.runtimeDescMap,{hostName:r.options.name,requestId:i},void 0,c.optionsToMFContext(r.options))}catch(e){throw await this.hooks.lifecycle.afterMatchRemote.emit({id:i,options:r.options,error:e,origin:r}),e}let{remote:d}=a,p=l.getRemoteInfo(d);await this.hooks.lifecycle.afterMatchRemote.emit({id:i,...a,options:r.options,remoteInfo:p,origin:r});let f=await r.sharedHandler.hooks.lifecycle.afterResolve.emit({id:i,...a,options:r.options,origin:r,remoteInfo:p}),{remote:h,expose:m}=f;o.assert(h&&m,`The 'beforeRequest' hook was executed, but it failed to return the correct 'remote' and 'expose' values while loading ${i}.`);let g=r.moduleCache.get(h.name),y={host:r,remoteInfo:p};return g||(g=new u.Module(y),r.moduleCache.set(h.name,g)),{module:g,moduleOptions:y,remoteMatchInfo:f}}registerRemote(e,t,r){let{host:n}=this,a=()=>{if(e.alias){let r=t.find(t=>{var r;return e.alias&&(t.name.startsWith(e.alias)||(null==(r=t.alias)?void 0:r.startsWith(e.alias)))});o.assert(!r,`The alias ${e.alias} of remote ${e.name} is not allowed to be the prefix of ${r&&r.name} name or alias`)}"entry"in e&&b.isBrowserEnvValue&&"undefined"!=typeof window&&!e.entry.startsWith("http")&&(e.entry=new URL(e.entry,window.location.origin).href),e.shareScope||(e.shareScope=i.DEFAULT_SCOPE),e.type||(e.type=i.DEFAULT_REMOTE_TYPE)};this.hooks.lifecycle.beforeRegisterRemote.emit({remote:e,origin:n});let s=t.find(t=>t.name===e.name);if(s){let o=[`The remote "${e.name}" is already registered.`,"Please note that overriding it may cause unexpected errors."];(null==r?void 0:r.force)&&(this.removeRemote(s),a(),t.push(e),this.hooks.lifecycle.registerRemote.emit({remote:e,origin:n}),(0,b.warn)(o.join(" ")))}else a(),t.push(e),this.hooks.lifecycle.registerRemote.emit({remote:e,origin:n})}removeRemote(e){try{let{host:r}=this,{name:o}=e,i=r.options.remotes.findIndex(e=>e.name===o);-1!==i&&r.options.remotes.splice(i,1);let s=r.moduleCache.get(e.name);if(s){var t;let o=s.remoteInfo,i=o.entryGlobalName;n.CurrentGlobal[i]&&((null==(t=Object.getOwnPropertyDescriptor(n.CurrentGlobal,i))?void 0:t.configurable)?delete n.CurrentGlobal[i]:n.CurrentGlobal[i]=void 0);let c=l.getRemoteEntryUniqueKey(s.remoteInfo);n.globalLoading[c]&&delete n.globalLoading[c],r.snapshotHandler.manifestCache.delete(o.entry);let d=o.buildVersion?(0,b.composeKeyWithSeparator)(o.name,o.buildVersion):o.name,u=n.CurrentGlobal.__FEDERATION__.__INSTANCES__.findIndex(e=>o.buildVersion?e.options.id===d:e.name===d);if(-1!==u){let e=n.CurrentGlobal.__FEDERATION__.__INSTANCES__[u];d=e.options.id||d;let t=a.getGlobalShareScope(),r=!0,i=[];Object.keys(t).forEach(e=>{let n=t[e];n&&Object.keys(n).forEach(t=>{let a=n[t];a&&Object.keys(a).forEach(n=>{let s=a[n];s&&Object.keys(s).forEach(a=>{let l=s[a];l&&"object"==typeof l&&l.from===o.name&&(l.loaded||l.loading?(l.useIn=l.useIn.filter(e=>e!==o.name),l.useIn.length?r=!1:i.push([e,t,n,a])):i.push([e,t,n,a]))})})})}),r&&(e.shareScopeMap={},delete t[d]),i.forEach(e=>{var r,o,n;let[i,a,s,l]=e;null==(n=t[i])||null==(o=n[a])||null==(r=o[s])||delete r[l]}),n.CurrentGlobal.__FEDERATION__.__INSTANCES__.splice(u,1)}let{hostGlobalSnapshot:p}=y.getGlobalRemoteInfo(e,r);if(p){let t=p&&"remotesInfo"in p&&p.remotesInfo&&n.getInfoWithoutType(p.remotesInfo,e.name).key;t&&(delete p.remotesInfo[t],n.Global.__FEDERATION__.__MANIFEST_LOADING__[t]&&delete n.Global.__FEDERATION__.__MANIFEST_LOADING__[t])}r.moduleCache.delete(e.name)}}catch(e){o.logger.error(`removeRemote failed: ${e instanceof Error?e.message:String(e)}`)}}constructor(e){this.hooks=new g.PluginSystem({beforeRegisterRemote:new h.SyncWaterfallHook("beforeRegisterRemote"),registerRemote:new h.SyncWaterfallHook("registerRemote"),beforeRequest:new m.AsyncWaterfallHook("beforeRequest"),afterMatchRemote:new f.AsyncHook("afterMatchRemote"),onLoad:new f.AsyncHook("onLoad"),afterLoadRemote:new f.AsyncHook("afterLoadRemote"),handlePreloadModule:new p.SyncHook("handlePreloadModule"),errorLoadRemote:new f.AsyncHook("errorLoadRemote"),beforePreloadRemote:new f.AsyncHook("beforePreloadRemote"),generatePreloadAssets:new f.AsyncHook("generatePreloadAssets"),afterPreloadRemote:new f.AsyncHook("afterPreloadRemote"),loadEntry:new f.AsyncHook}),this.host=e,this.idToRemoteMap={}}}},994:function(e,t,r){let o=r(9869),n=r(4696),i=r(5203),a=r(1607);r(2098);let s=r(669),l=r(4514),c=r(2187),d=r(6537),u=r(6359);r(6623);let p=r(8933);t.SharedHandler=class{emitAfterLoadShare(e){let{lifecycle:t,pkgName:r,shareInfo:n,selectedShared:i}=e;try{this.hooks.lifecycle.afterLoadShare.emit({pkgName:r,shareInfo:n,selectedShared:i,shared:this.host.options.shared,shareScopeMap:this.shareScopeMap,lifecycle:t,origin:this.host})}catch(e){o.warn(e)}}emitErrorLoadShare(e){let{lifecycle:t,pkgName:r,shareInfo:n,error:i,recovered:a}=e;try{this.hooks.lifecycle.errorLoadShare.emit({pkgName:r,shareInfo:n,shared:this.host.options.shared,shareScopeMap:this.shareScopeMap,lifecycle:t,origin:this.host,error:i,recovered:a})}catch(e){o.warn(e)}}registerShared(e,t){let{newShareInfos:r,allShareInfos:o}=i.formatShareConfigs(e,t);return Object.keys(r).forEach(e=>{r[e].forEach(r=>{r.scope.forEach(o=>{var n;this.hooks.lifecycle.beforeRegisterShare.emit({origin:this.host,pkgName:e,shared:r}),(null==(n=this.shareScopeMap[o])?void 0:n[e])||this.setShared({pkgName:e,lib:r.lib,get:r.get,loaded:r.loaded||!!r.lib,shared:r,from:t.name})})})}),{newShareInfos:r,allShareInfos:o}}async loadShare(e,t){let{host:r}=this,n=i.getTargetSharedOptions({pkgName:e,extraOptions:t,shareInfos:r.options.shared}),a=n;try{(null==n?void 0:n.scope)&&await Promise.all(n.scope.map(async e=>{await Promise.all(this.initializeSharing(e,{strategy:n.strategy}))})),a=(await this.hooks.lifecycle.beforeLoadShare.emit({pkgName:e,shareInfo:n,shared:r.options.shared,origin:r})).shareInfo,o.assert(a,`Cannot find shared "${e}" in host "${r.options.name}". Ensure the shared config for "${e}" is declared in the federation plugin options and the host has been initialized before loading shares.`);let s=a,{shared:l,useTreesShaking:c}=i.getRegisteredShare(this.shareScopeMap,e,a,this.hooks.lifecycle.resolveShare)||{};if(l){let t=i.directShare(l,c);if(t.lib)return i.addUseIn(t,r.options.name),this.emitAfterLoadShare({lifecycle:"loadShare",pkgName:e,shareInfo:s,selectedShared:l}),t.lib;if(t.loading&&!t.loaded){let o=await t.loading;return t.loaded=!0,t.lib||(t.lib=o),i.addUseIn(t,r.options.name),this.emitAfterLoadShare({lifecycle:"loadShare",pkgName:e,shareInfo:s,selectedShared:l}),o}{let o=(async()=>{let e=await t.get();return i.addUseIn(t,r.options.name),t.loaded=!0,t.lib=e,e})();this.setShared({pkgName:e,loaded:!1,shared:l,from:r.options.name,lib:null,loading:o,treeShaking:c?t:void 0});let n=await o;return this.emitAfterLoadShare({lifecycle:"loadShare",pkgName:e,shareInfo:s,selectedShared:l}),n}}{if(null==t?void 0:t.customShareInfo)return this.emitErrorLoadShare({lifecycle:"loadShare",pkgName:e,shareInfo:s,recovered:!0}),!1;let o=i.shouldUseTreeShaking(s.treeShaking),n=i.directShare(s,o),a=(async()=>{let t=await n.get();n.lib=t,n.loaded=!0,i.addUseIn(n,r.options.name);let{shared:o,useTreesShaking:a}=i.getRegisteredShare(this.shareScopeMap,e,s,this.hooks.lifecycle.resolveShare)||{};if(o){let e=i.directShare(o,a);e.lib=t,e.loaded=!0,o.from=s.from}return t})();this.setShared({pkgName:e,loaded:!1,shared:s,from:r.options.name,lib:null,loading:a,treeShaking:o?n:void 0});let l=await a;return this.emitAfterLoadShare({lifecycle:"loadShare",pkgName:e,shareInfo:s,selectedShared:s}),l}}catch(t){throw this.emitErrorLoadShare({lifecycle:"loadShare",pkgName:e,shareInfo:a,error:t}),t}}initializeSharing(){let e=arguments.length>0&&void 0!==arguments[0]?arguments[0]:n.DEFAULT_SCOPE,t=arguments.length>1?arguments[1]:void 0,{host:r}=this,o=null==t?void 0:t.from,a=null==t?void 0:t.strategy,s=null==t?void 0:t.initScope,l=[];if("build"!==o){let{initTokens:t}=this;s||(s=[]);let r=t[e];if(r||(r=t[e]={from:this.host.name}),s.indexOf(r)>=0)return l;s.push(r)}let c=this.shareScopeMap,d=r.options.name;c[e]||(c[e]={});let u=c[e],p=(e,t)=>{var r;let{version:o,eager:n}=t;u[e]=u[e]||{};let a=u[e],s=a[o]&&i.directShare(a[o]),l=!!(s&&("eager"in s&&s.eager||"shareConfig"in s&&(null==(r=s.shareConfig)?void 0:r.eager)));(!s||"loaded-first"!==s.strategy&&!s.loaded&&(!n!=!l?n:d>a[o].from))&&(a[o]=t)},f=async e=>{let t,{module:o}=await r.remoteHandler.getRemoteModuleAndOptions({id:e});try{t=await o.getEntry()}catch(n){if(!(t=await r.remoteHandler.hooks.lifecycle.errorLoadRemote.emit({id:e,error:n,from:"runtime",lifecycle:"beforeLoadShare",remote:o.remoteInfo,origin:r})))return}finally{(null==t?void 0:t.init)&&!o.initing&&(o.remoteEntryExports=t,await o.init(void 0,void 0,s))}};return Object.keys(r.options.shared).forEach(t=>{r.options.shared[t].forEach(r=>{r.scope.includes(e)&&p(t,r)})}),("version-first"===r.options.shareStrategy||"version-first"===a)&&r.options.remotes.forEach(t=>{t.shareScope===e&&l.push(f(t.name))}),l}loadShareSync(e,t){let{host:r}=this,n=i.getTargetSharedOptions({pkgName:e,extraOptions:t,shareInfos:r.options.shared});try{(null==n?void 0:n.scope)&&n.scope.forEach(e=>{this.initializeSharing(e,{strategy:n.strategy})});let{shared:s}=i.getRegisteredShare(this.shareScopeMap,e,n,this.hooks.lifecycle.resolveShare)||{};if(s){if("function"==typeof s.lib)return i.addUseIn(s,r.options.name),s.loaded||(s.loaded=!0,s.from===r.options.name&&(n.loaded=!0)),this.emitAfterLoadShare({lifecycle:"loadShareSync",pkgName:e,shareInfo:n,selectedShared:s}),s.lib;if("function"==typeof s.get){let t=s.get();if(!(t instanceof Promise))return i.addUseIn(s,r.options.name),this.setShared({pkgName:e,loaded:!0,from:r.options.name,lib:t,shared:s}),this.emitAfterLoadShare({lifecycle:"loadShareSync",pkgName:e,shareInfo:n,selectedShared:s}),t}}if(n.lib)return n.loaded||(n.loaded=!0),this.emitAfterLoadShare({lifecycle:"loadShareSync",pkgName:e,shareInfo:n,selectedShared:n}),n.lib;if(n.get){let i=n.get();return i instanceof Promise&&o.error((null==t?void 0:t.from)==="build"?p.RUNTIME_005:p.RUNTIME_006,p.runtimeDescMap,{hostName:r.options.name,sharedPkgName:e},void 0,a.optionsToMFContext(r.options)),n.lib=i,this.setShared({pkgName:e,loaded:!0,from:r.options.name,lib:n.lib,shared:n}),this.emitAfterLoadShare({lifecycle:"loadShareSync",pkgName:e,shareInfo:n,selectedShared:n}),n.lib}o.error(p.RUNTIME_006,p.runtimeDescMap,{hostName:r.options.name,sharedPkgName:e},void 0,a.optionsToMFContext(r.options))}catch(t){throw this.emitErrorLoadShare({lifecycle:"loadShareSync",pkgName:e,shareInfo:n,error:t}),t}}initShareScopeMap(e,t){let r=arguments.length>2&&void 0!==arguments[2]?arguments[2]:{},{host:o}=this;this.shareScopeMap[e]=t,this.hooks.lifecycle.initContainerShareScopeMap.emit({shareScope:t,options:o.options,origin:o,scopeName:e,hostShareScopeMap:r.hostShareScopeMap})}setShared(e){let{pkgName:t,shared:r,from:o,lib:n,loading:i,loaded:a,get:s,treeShaking:l}=e,{version:c,scope:d="default",...u}=r,p=Array.isArray(d)?d:[d],f=e=>{let t=(e,t,r)=>{r&&!e[t]&&(e[t]=r)},r=l?e.treeShaking:e;t(r,"loaded",a),t(r,"loading",i),t(r,"get",s)};p.forEach(e=>{this.shareScopeMap[e]||(this.shareScopeMap[e]={}),this.shareScopeMap[e][t]||(this.shareScopeMap[e][t]={}),this.shareScopeMap[e][t][c]||(this.shareScopeMap[e][t][c]={version:c,scope:[e],...u,lib:n});let r=this.shareScopeMap[e][t][c];f(r),o&&r.from!==o&&(r.from=o)})}_setGlobalShareScopeMap(e){let t=i.getGlobalShareScope(),r=e.id||e.name;r&&!t[r]&&(t[r]=this.shareScopeMap)}constructor(e){this.hooks=new u.PluginSystem({beforeRegisterShare:new c.SyncWaterfallHook("beforeRegisterShare"),afterResolve:new d.AsyncWaterfallHook("afterResolve"),beforeLoadShare:new d.AsyncWaterfallHook("beforeLoadShare"),loadShare:new l.AsyncHook,afterLoadShare:new s.SyncHook("afterLoadShare"),errorLoadShare:new s.SyncHook("errorLoadShare"),resolveShare:new c.SyncWaterfallHook("resolveShare"),initContainerShareScopeMap:new c.SyncWaterfallHook("initContainerShareScopeMap")}),this.host=e,this.shareScopeMap={},this.initTokens={},this._setGlobalShareScopeMap(e.options)}}},8957:function(e,t,r){var o=r(6350).__exportAll({});Object.defineProperty(t,"type_exports",{enumerable:!0,get:function(){return o}})},1607:function(e,t){function r(e){return{name:e.name,alias:e.alias,entry:"entry"in e?e.entry:void 0,version:"version"in e?e.version:void 0,type:e.type,entryGlobalName:e.entryGlobalName,shareScope:e.shareScope}}t.optionsToMFContext=function(e){var t,o,n,i,a,s;let l={};for(let[t,r]of Object.entries(e.shared)){let e=r[0];e&&(l[t]={version:e.version,singleton:null==(n=e.shareConfig)?void 0:n.singleton,requiredVersion:(null==(i=e.shareConfig)?void 0:i.requiredVersion)!==!1&&(null==(a=e.shareConfig)?void 0:a.requiredVersion),eager:e.eager,strictVersion:null==(s=e.shareConfig)?void 0:s.strictVersion})}return{project:{name:e.name,mfRole:(null==(t=e.remotes)?void 0:t.length)>0?"host":"unknown"},mfConfig:{name:e.name,remotes:(null==(o=e.remotes)?void 0:o.map(r))??[],shared:l}}}},2911:function(e,t,r){r(5652),t.getBuilderId=function(){return"coreshoporder:1.0.0"}},4514:function(e,t,r){let o=r(669);t.AsyncHook=class extends o.SyncHook{emit(){let e;for(var t=arguments.length,r=Array(t),o=0;o<t;o++)r[o]=arguments[o];let n=Array.from(this.listeners);if(n.length>0){let t=0,o=e=>!1!==e&&(t<n.length?Promise.resolve(n[t++].apply(null,r)).then(t=>void 0===t||1===r.length&&t===r[0]?o(e):o(t)):e);e=o()}return Promise.resolve(e)}}},6537:function(e,t,r){let o=r(9869),n=r(8844),i=r(669),a=r(2187);t.AsyncWaterfallHook=class extends i.SyncHook{emit(e){n.isObject(e)||o.error(`The response data for the "${this.type}" hook must be an object.`);let t=Array.from(this.listeners);if(t.length>0){let r=0,n=t=>(o.warn(t),this.onerror(t),e),i=o=>{if(void 0!==o&&a.checkReturnData(e,o))e=o;else if(void 0!==o)return this.onerror(`A plugin returned an incorrect value for the "${this.type}" type.`),e;if(r<t.length)try{return Promise.resolve(t[r++](e)).then(i,n)}catch(e){return n(e)}return e};return Promise.resolve(i(e))}return Promise.resolve(e)}constructor(e){super(),this.onerror=o.error,this.type=e}}},6623:function(e,t,r){r(669),r(4514),r(2187),r(6537),r(6359)},6359:function(e,t,r){let o=r(9869),n=r(8844);r(2098),t.PluginSystem=class{applyPlugin(e,t){o.assert(n.isPlainObject(e),"Plugin configuration is invalid.");let r=e.name;if(o.assert(r,"A name must be provided by the plugin."),!this.registerPlugins[r]){var i;this.registerPlugins[r]=e,null==(i=e.apply)||i.call(e,t),Object.keys(this.lifecycle).forEach(t=>{let r=e[t];r&&this.lifecycle[t].on(r)})}}removePlugin(e){o.assert(e,"A name is required.");let t=this.registerPlugins[e];o.assert(t,`The plugin "${e}" is not registered.`),Object.keys(t).forEach(e=>{"name"!==e&&this.lifecycle[e].remove(t[e])})}constructor(e){this.registerPlugins={},this.lifecycle=e,this.lifecycleKeys=Object.keys(e)}}},669:function(e,t){t.SyncHook=class{on(e){"function"==typeof e&&this.listeners.add(e)}once(e){let t=this;this.on(function r(){for(var o=arguments.length,n=Array(o),i=0;i<o;i++)n[i]=arguments[i];return t.remove(r),e.apply(null,n)})}emit(){let e;for(var t=arguments.length,r=Array(t),o=0;o<t;o++)r[o]=arguments[o];return this.listeners.size>0&&this.listeners.forEach(t=>{let o=t(...r);void 0!==o&&(e=o)}),e}remove(e){this.listeners.delete(e)}removeAll(){this.listeners.clear()}constructor(e){this.type="",this.listeners=new Set,e&&(this.type=e)}}},2187:function(e,t,r){let o=r(9869),n=r(8844),i=r(669);function a(e,t){if(!n.isObject(t))return!1;if(e!==t){for(let r in e)if(!(r in t))return!1}return!0}t.SyncWaterfallHook=class extends i.SyncHook{emit(e){for(let t of(n.isObject(e)||o.error(`The data for the "${this.type}" hook should be an object.`),this.listeners))try{let r=t(e);if(void 0===r)continue;if(a(e,r))e=r;else{this.onerror(`A plugin returned an unacceptable value for the "${this.type}" type.`);break}}catch(e){o.warn(e),this.onerror(e)}return e}constructor(e){super(),this.onerror=o.error,this.type=e}},t.checkReturnData=a},2098:function(e,t,r){r(9869),r(8844),r(2911),r(5241),r(3687),r(2726),r(1607),r(5652)},2726:function(e,t,r){let o=r(9869),n=r(201),i=r(4696),a=r(5652),s=r(8933),l=".then(callbacks[0]).catch(callbacks[1])";async function c(e){let{entry:t,remoteEntryExports:r}=e;return new Promise((e,n)=>{try{r?e(r):"undefined"!=typeof FEDERATION_ALLOW_NEW_FUNCTION?Function("callbacks",`import("${t}")${l}`)([e,n]):import(t).then(e).catch(n)}catch(e){o.error(`Failed to load ESM entry from "${t}". ${e instanceof Error?e.message:String(e)}`)}})}async function d(e){let{entry:t,remoteEntryExports:r}=e;return new Promise((e,n)=>{try{r?e(r):Function("callbacks",`System.import("${t}")${l}`)([e,n])}catch(e){o.error(`Failed to load SystemJS entry from "${t}". ${e instanceof Error?e.message:String(e)}`)}})}function u(e,t,r){let{remoteEntryKey:i,entryExports:a}=n.getRemoteEntryExports(e,t);return a||o.error(s.RUNTIME_001,s.runtimeDescMap,{remoteName:e,remoteEntryUrl:r,remoteEntryKey:i}),a}async function p(e){let{name:t,globalName:r,entry:i,remoteInfo:l,loaderHook:c,getEntryUrl:d,resourceContext:p}=e,{entryExports:f}=n.getRemoteEntryExports(t,r);if(f)return f;let h=d?d(i):i;return(0,a.loadScript)(h,{attrs:{},createScriptHook:(e,t)=>{let r=c.lifecycle.createScript.emit({url:e,attrs:t,remoteInfo:l,resourceContext:p?{...p,url:e}:void 0});if(r&&(r instanceof HTMLScriptElement||"script"in r||"timeout"in r))return r}}).then(()=>u(t,r,i),e=>{let r=e instanceof Error?e.message:String(e);o.error(s.RUNTIME_008,s.runtimeDescMap,{remoteName:t,resourceUrl:h},r)})}async function f(e){let{remoteInfo:t,remoteEntryExports:r,loaderHook:o,getEntryUrl:n,resourceContext:i}=e,{entry:a,entryGlobalName:s,name:l,type:u}=t;switch(u){case"esm":case"module":return c({entry:a,remoteEntryExports:r});case"system":return d({entry:a,remoteEntryExports:r});default:return p({entry:a,globalName:s,name:l,remoteInfo:t,loaderHook:o,getEntryUrl:n,resourceContext:i})}}async function h(e){let{remoteInfo:t,loaderHook:r,resourceContext:i}=e,{entry:s,entryGlobalName:l,name:c,type:d}=t,{entryExports:p}=n.getRemoteEntryExports(c,l);return p||(0,a.loadScriptNode)(s,{attrs:{name:c,globalName:l,type:d},loaderHook:{createScriptHook:function(e){let o=arguments.length>1&&void 0!==arguments[1]?arguments[1]:{},n=r.lifecycle.createScript.emit({url:e,attrs:o,remoteInfo:t,resourceContext:i?{...i,url:e}:void 0});if(n&&"url"in n)return n}}}).then(()=>u(c,l,s)).catch(e=>{o.error(`Failed to load Node.js entry for remote "${c}" from "${s}". ${e instanceof Error?e.message:String(e)}`)})}function m(e){let{entry:t,name:r}=e;return(0,a.composeKeyWithSeparator)(r,t)}async function g(e){let{origin:t,remoteEntryExports:r,remoteInfo:o,getEntryUrl:i,resourceContext:l,_inErrorHandling:c=!1}=e,d=m(o);if(r)return r;if(!n.globalLoading[d]){let e=t.remoteHandler.hooks.lifecycle.loadEntry,u=t.loaderHook;n.globalLoading[d]=e.emit({origin:t,loaderHook:u,remoteInfo:o,remoteEntryExports:r}).then(e=>e||(("undefined"!=typeof ENV_TARGET?"web"===ENV_TARGET:a.isBrowserEnvValue)?f({remoteInfo:o,remoteEntryExports:r,loaderHook:u,getEntryUrl:i,resourceContext:l}):h({remoteInfo:o,loaderHook:u,resourceContext:l}))).then(async e=>(await t.loaderHook.lifecycle.afterLoadEntry.emit({origin:t,remoteInfo:o,remoteEntryExports:e}),e)).catch(async e=>{let i=m(o),a=e instanceof Error&&e.message.includes("ScriptExecutionError");if(e instanceof Error&&e.message.includes(s.RUNTIME_008)&&!a&&!c){let e=e=>g({...e,_inErrorHandling:!0}),a=await t.loaderHook.lifecycle.loadEntryError.emit({getRemoteEntry:e,origin:t,remoteInfo:o,remoteEntryExports:r,globalLoading:n.globalLoading,uniqueKey:i});if(a)return await t.loaderHook.lifecycle.afterLoadEntry.emit({origin:t,remoteInfo:o,remoteEntryExports:a,recovered:!0}),a}throw await t.loaderHook.lifecycle.afterLoadEntry.emit({origin:t,remoteInfo:o,error:e}),e})}return n.globalLoading[d]}function y(e){return{...e,entry:"entry"in e?e.entry:"",type:e.type||i.DEFAULT_REMOTE_TYPE,entryGlobalName:e.entryGlobalName||e.name,shareScope:e.shareScope||i.DEFAULT_SCOPE}}t.getRemoteEntry=g,t.getRemoteEntryUniqueKey=m,t.getRemoteInfo=y},9869:function(e,t,r){let o=r(5652),n=r(6953),i="[ Federation Runtime ]",a=(0,o.createLogger)(i);function s(e,t,r,o,a){if(void 0!==t)return(0,n.logAndReport)(e,t,r??{},e=>{throw Error(`${i}: ${e}`)},o,a);let s=e;if(s instanceof Error)throw s.message.startsWith(i)||(s.message=`${i}: ${s.message}`),s;throw Error(`${i}: ${s}`)}function l(e){e instanceof Error&&(e.message.startsWith(i)||(e.message=`${i}: ${e.message}`)),a.warn(e)}t.assert=function(e,t,r,o,n){e||(void 0!==r?s(t,r,o,void 0,n):s(t))},t.error=s,t.logger=a,t.warn=l},5241:function(e,t){function r(e,t){for(let r of e){let e=t.startsWith(r.name),o=t.replace(r.name,"");if(e){if(o.startsWith("/"))return{pkgNameOrAlias:r.name,expose:o=`.${o}`,remote:r};else if(""===o)return{pkgNameOrAlias:r.name,expose:".",remote:r}}let n=r.alias&&t.startsWith(r.alias),i=r.alias&&t.replace(r.alias,"");if(r.alias&&n){if(i&&i.startsWith("/"))return{pkgNameOrAlias:r.alias,expose:i=`.${i}`,remote:r};else if(""===i)return{pkgNameOrAlias:r.alias,expose:".",remote:r}}}}function o(e,t){for(let r of e)if(t===r.name||r.alias&&t===r.alias)return r}t.composeRemoteRequestId=function(e,t){return t&&"."!==t?`${e}/${t.replace(/^\.\//,"")}`:e},t.matchRemote=o,t.matchRemoteWithNameAndExpose=r},3687:function(e,t,r){let o=r(201);t.registerPlugins=function(e,t){let r=o.getGlobalHostPlugins(),n=[t.hooks,t.remoteHandler.hooks,t.sharedHandler.hooks,t.snapshotHandler.hooks,t.loaderHook,t.bridgeHook];return r.length>0&&r.forEach(t=>{(null==e?void 0:e.find(e=>e.name!==t.name))&&e.push(t)}),e&&e.length>0&&e.forEach(e=>{n.forEach(r=>{r.applyPlugin(e,t)})}),e}},6241:function(e,t,r){let o=r(9869),n=r(5241),i=r(2726),a=r(5652);function s(e){return{resourceCategory:"sync",share:!0,depsRemote:!0,...e}}function l(e,t){return t.map(t=>{let r=n.matchRemote(e,t.nameOrAlias);return o.assert(r,`Unable to preload ${t.nameOrAlias} as it is not included in ${!r&&(0,a.safeToString)({remoteInfo:r,remotes:e})}`),{remote:r,preloadConfig:s(t)}})}function c(e){return e?e.map(e=>"."===e?e:e.startsWith("./")?e.replace("./",""):e):[]}function d(e){return e instanceof Error&&(e.message.includes("timed out")||e.name.includes("Timeout"))}function u(e,t,r,o){return{url:t,status:r,resourceType:e.resourceType,initiator:e.initiator,id:e.id,error:o}}async function p(e,t,r,o){let n=e.moduleCache.get(r.name),a=r.entry;if(null==n?void 0:n.remoteEntryExports)return u(o,a,"cached");try{if(!await i.getRemoteEntry({origin:e,remoteInfo:r,remoteEntryExports:null==n?void 0:n.remoteEntryExports,resourceContext:{...o,url:a}}))throw Error(`Failed to load remoteEntry "${a}".`);return u(o,a,"success")}catch(e){return u(o,a,d(e)?"timeout":"error",e)}}function f(e){let{host:t,remoteInfo:r,url:o,attrs:n,context:i,needDeleteLink:s}=e;return new Promise(e=>{let{link:l,needAttach:c}=(0,a.createLink)({url:o,cb:()=>{e(u(i,o,c?"success":"cached"))},onErrorCallback:t=>{e(u(i,o,d(t)?"timeout":"error",t))},attrs:n,createLinkHook:(e,o)=>{let n=t.loaderHook.lifecycle.createLink.emit({url:e,attrs:o,remoteInfo:r,resourceContext:{...i,url:e}});return n instanceof HTMLLinkElement,n},needDeleteLink:s});c&&document.head.appendChild(l)})}function h(e){let{host:t,remoteInfo:r,url:o,attrs:n,context:i}=e;return new Promise(e=>{let{script:s,needAttach:l}=(0,a.createScript)({url:o,cb:()=>{e(u(i,o,l?"success":"cached"))},onErrorCallback:t=>{e(u(i,o,d(t)?"timeout":"error",t))},attrs:n,createScriptHook:(e,o)=>{let n=t.loaderHook.lifecycle.createScript.emit({url:e,attrs:o,remoteInfo:r,resourceContext:{...i,url:e}});return n instanceof HTMLScriptElement,n},needDeleteScript:!0});l&&document.head.appendChild(s)})}function m(e,t){return{...e,resourceType:t}}function g(e,t,r){let o=!(arguments.length>3)||void 0===arguments[3]||arguments[3],n=arguments.length>4&&void 0!==arguments[4]?arguments[4]:{initiator:"preloadRemote",id:e.name},{cssAssets:i,jsAssetsWithoutEntry:a,entryAssets:s}=r,l=[];if(t.options.inBrowser){if(s.forEach(r=>{let{moduleInfo:o}=r;l.push(p(t,e,o,m(n,"remoteEntry")))}),o){let r={rel:"preload",as:"style"};i.forEach(o=>{l.push(f({host:t,remoteInfo:e,url:o,attrs:r,context:m(n,"css")}))})}else{let r={rel:"stylesheet",type:"text/css"};i.forEach(o=>{l.push(f({host:t,remoteInfo:e,url:o,attrs:r,needDeleteLink:!1,context:m(n,"css")}))})}if(o){let r={rel:"preload",as:"script"};a.forEach(o=>{l.push(f({host:t,remoteInfo:e,url:o,attrs:r,context:m(n,"js")}))})}else{let r={fetchpriority:"high",type:(null==e?void 0:e.type)==="module"?"module":"text/javascript"};a.forEach(o=>{l.push(h({host:t,remoteInfo:e,url:o,attrs:r,context:m(n,"js")}))})}}return Promise.all(l)}t.defaultPreloadArgs=s,t.formatPreloadArgs=l,t.normalizePreloadExposes=c,t.preloadAssets=g},842:function(e,t){function r(e,t){return(e=Number(e)||e)>(t=Number(t)||t)?1:e===t?0:-1}function o(e,t){let{preRelease:o}=e,{preRelease:n}=t;if(void 0===o&&n)return 1;if(o&&void 0===n)return -1;if(void 0===o&&void 0===n)return 0;for(let e=0,t=o.length;e<=t;e++){let t=o[e],i=n[e];if(t!==i){if(void 0===t&&void 0===i)return 0;if(!t)return 1;if(!i)return -1;return r(t,i)}}return 0}function n(e,t){return r(e.major,t.major)||r(e.minor,t.minor)||r(e.patch,t.patch)||o(e,t)}function i(e,t){return e.version===t.version}t.compare=function(e,t){switch(e.operator){case"":case"=":return i(e,t);case">":return 0>n(e,t);case">=":return i(e,t)||0>n(e,t);case"<":return n(e,t)>0;case"<=":return i(e,t)||n(e,t)>0;case void 0:return!0;default:return!1}}},8635:function(e,t){let r="[0-9A-Za-z-]+",o=`(?:\\+(${r}(?:\\.${r})*))`,n="0|[1-9]\\d*",i="[0-9]+",a="\\d*[a-zA-Z-][a-zA-Z0-9-]*",s=`(?:${i}|${a})`,l=`(?:-?(${s}(?:\\.${s})*))`,c=`(?:${n}|${a})`,d=`(?:-(${c}(?:\\.${c})*))`,u=`${n}|x|X|\\*`,p=`[v=\\s]*(${u})(?:\\.(${u})(?:\\.(${u})(?:${d})?${o}?)?)?`,f=`^\\s*(${p})\\s+-\\s+(${p})\\s*$`,h=`[v=\\s]*${`(${i})\\.(${i})\\.(${i})`}${l}?${o}?`,m="((?:<|>)?=?)",g=`(\\s*)${m}\\s*(${h}|${p})`,y="(?:~>?)",b=`(\\s*)${y}\\s+`,E="(?:\\^)",S=`(\\s*)${E}\\s+`,x="(<|>)?=?\\s*\\*",_=`^${E}${p}$`,v=`v?${`(${n})\\.(${n})\\.(${n})`}${d}?${o}?`,R=`^${y}${p}$`,I=`^${m}\\s*${p}$`,T=`^${m}\\s*(${v})$|^$`,w="^\\s*>=\\s*0.0.0\\s*$";t.caret=_,t.caretTrim=S,t.comparator=T,t.comparatorTrim=g,t.gte0=w,t.hyphenRange=f,t.star=x,t.tilde=R,t.tildeTrim=b,t.xRange=I},3743:function(e,t,r){let o=r(4088),n=r(4176),i=r(842);function a(e){return o.pipe(n.parseCarets,n.parseTildes,n.parseXRanges,n.parseStar)(e)}function s(e){return o.pipe(n.parseHyphen,n.parseComparatorTrim,n.parseTildeTrim,n.parseCaretTrim)(e.trim()).split(/\s+/).join(" ")}t.satisfy=function(e,t){if(!e)return!1;let r=o.extractComparator(e);if(!r)return!1;let[,l,,c,d,u,p]=r,f={operator:l,version:o.combineVersion(c,d,u,p),major:c,minor:d,patch:u,preRelease:null==p?void 0:p.split(".")};for(let e of t.split("||")){let t=e.trim();if(!t||"*"===t||"x"===t)return!0;try{let e=s(t);if(!e.trim())return!0;let r=e.split(" ").map(e=>a(e)).join(" ");if(!r.trim())return!0;let l=r.split(/\s+/).map(e=>n.parseGTE0(e)).filter(Boolean);if(0===l.length)continue;let c=!0;for(let e of l){let t=o.extractComparator(e);if(!t){c=!1;break}let[,r,,n,a,s,l]=t;if(!i.compare({operator:r,version:o.combineVersion(n,a,s,l),major:n,minor:a,patch:s,preRelease:null==l?void 0:l.split(".")},f)){c=!1;break}}if(c)return!0}catch(e){console.error(`[semver] Error processing range part "${t}":`,e);continue}}return!1}},4176:function(e,t,r){let o=r(8635),n=r(4088);function i(e){return e.replace(n.parseRegex(o.hyphenRange),(e,t,r,o,i,a,s,l,c,d,u,p)=>(t=n.isXVersion(r)?"":n.isXVersion(o)?`>=${r}.0.0`:n.isXVersion(i)?`>=${r}.${o}.0`:`>=${t}`,l=n.isXVersion(c)?"":n.isXVersion(d)?`<${Number(c)+1}.0.0-0`:n.isXVersion(u)?`<${c}.${Number(d)+1}.0-0`:p?`<=${c}.${d}.${u}-${p}`:`<=${l}`,`${t} ${l}`.trim()))}function a(e){return e.replace(n.parseRegex(o.comparatorTrim),"$1$2$3")}function s(e){return e.replace(n.parseRegex(o.tildeTrim),"$1~")}function l(e){return e.trim().split(/\s+/).map(e=>e.replace(n.parseRegex(o.caret),(e,t,r,o,i)=>{if(n.isXVersion(t))return"";if(n.isXVersion(r))return`>=${t}.0.0 <${Number(t)+1}.0.0-0`;if(n.isXVersion(o))if("0"===t)return`>=${t}.${r}.0 <${t}.${Number(r)+1}.0-0`;else return`>=${t}.${r}.0 <${Number(t)+1}.0.0-0`;if(i)if("0"!==t)return`>=${t}.${r}.${o}-${i} <${Number(t)+1}.0.0-0`;else if("0"===r)return`>=${t}.${r}.${o}-${i} <${t}.${r}.${Number(o)+1}-0`;else return`>=${t}.${r}.${o}-${i} <${t}.${Number(r)+1}.0-0`;if("0"===t)if("0"===r)return`>=${t}.${r}.${o} <${t}.${r}.${Number(o)+1}-0`;else return`>=${t}.${r}.${o} <${t}.${Number(r)+1}.0-0`;return`>=${t}.${r}.${o} <${Number(t)+1}.0.0-0`})).join(" ")}function c(e){return e.trim().split(/\s+/).map(e=>e.replace(n.parseRegex(o.tilde),(e,t,r,o,i)=>n.isXVersion(t)?"":n.isXVersion(r)?`>=${t}.0.0 <${Number(t)+1}.0.0-0`:n.isXVersion(o)?`>=${t}.${r}.0 <${t}.${Number(r)+1}.0-0`:i?`>=${t}.${r}.${o}-${i} <${t}.${Number(r)+1}.0-0`:`>=${t}.${r}.${o} <${t}.${Number(r)+1}.0-0`)).join(" ")}function d(e){return e.split(/\s+/).map(e=>e.trim().replace(n.parseRegex(o.xRange),(e,t,r,o,i,a)=>{let s=n.isXVersion(r),l=s||n.isXVersion(o),c=l||n.isXVersion(i);if("="===t&&c&&(t=""),a="",s)if(">"===t||"<"===t)return"<0.0.0-0";else return"*";return t&&c?(l&&(o=0),i=0,">"===t?(t=">=",l?(r=Number(r)+1,o=0):o=Number(o)+1,i=0):"<="===t&&(t="<",l?r=Number(r)+1:o=Number(o)+1),"<"===t&&(a="-0"),`${t+r}.${o}.${i}${a}`):l?`>=${r}.0.0${a} <${Number(r)+1}.0.0-0`:c?`>=${r}.${o}.0${a} <${r}.${Number(o)+1}.0-0`:e})).join(" ")}function u(e){return e.trim().replace(n.parseRegex(o.star),"")}function p(e){return e.trim().replace(n.parseRegex(o.gte0),"")}t.parseCaretTrim=function(e){return e.replace(n.parseRegex(o.caretTrim),"$1^")},t.parseCarets=l,t.parseComparatorTrim=a,t.parseGTE0=p,t.parseHyphen=i,t.parseStar=u,t.parseTildeTrim=s,t.parseTildes=c,t.parseXRanges=d},4088:function(e,t,r){let o=r(8635);function n(e){return new RegExp(e)}function i(e){return!e||"x"===e.toLowerCase()||"*"===e}function a(){for(var e=arguments.length,t=Array(e),r=0;r<e;r++)t[r]=arguments[r];return e=>t.reduce((e,t)=>t(e),e)}function s(e){return e.match(n(o.comparator))}t.combineVersion=function(e,t,r,o){let n=`${e}.${t}.${r}`;return o?`${n}-${o}`:n},t.extractComparator=s,t.isXVersion=i,t.parseRegex=n,t.pipe=a},5203:function(e,t,r){let o=r(9869),n=r(8844),i=r(201),a=r(4696),s=r(3743),l=r(5652);function c(e,t,r,n){var i,a;let s;return s="get"in e?e.get:"lib"in e?()=>Promise.resolve(e.lib):()=>Promise.resolve(()=>{o.error(`Cannot get shared "${r}" from "${t}": neither "get" nor "lib" is provided in the share config.`)}),(null==(i=e.shareConfig)?void 0:i.eager)&&(null==(a=e.treeShaking)?void 0:a.mode)&&o.error(`Invalid shared config for "${r}" from "${t}": cannot use both "eager: true" and "treeShaking.mode" simultaneously. Choose one strategy.`),{deps:[],useIn:[],from:t,loading:null,...e,shareConfig:{requiredVersion:`^${e.version}`,singleton:!1,eager:!1,strictVersion:!1,...e.shareConfig},get:s,loaded:null!=e&&!!e.loaded||"lib"in e||void 0,version:e.version??"0",scope:Array.isArray(e.scope)?e.scope:[e.scope??"default"],strategy:(e.strategy??n)||"version-first",treeShaking:e.treeShaking?{...e.treeShaking,mode:e.treeShaking.mode??"server-calc",status:e.treeShaking.status??l.TreeShakingStatus.UNKNOWN,useIn:[]}:void 0}}function d(e,t){let r=t.shared||{},o=t.name,i=Object.keys(r).reduce((e,i)=>{let a=n.arrayOptions(r[i]);return e[i]=e[i]||[],a.forEach(r=>{e[i].push(c(r,o,i,t.shareStrategy))}),e},{}),a={...e.shared};return Object.keys(i).forEach(e=>{a[e]?i[e].forEach(t=>{a[e].find(e=>e.version===t.version)||a[e].push(t)}):a[e]=i[e]}),{allShareInfos:a,newShareInfos:i}}function u(e,t){if(!e)return!1;let{status:r,mode:o}=e;return r!==l.TreeShakingStatus.NO_USE&&(r===l.TreeShakingStatus.CALCULATED||"runtime-infer"===o&&(!t||g(e,t)))}function p(e,t){let r=e=>{if(!Number.isNaN(Number(e))){let t=e.split("."),r=e;for(let e=0;e<3-t.length;e++)r+=".0";return r}return e};return!!s.satisfy(r(e),`<=${r(t)}`)}let f=(e,t)=>{let r=t||function(e,t){return p(e,t)};return Object.keys(e).reduce((e,t)=>!e||r(e,t)||"0"===e?t:e,0)},h=e=>!!e.loaded||"function"==typeof e.lib,m=e=>!!e.loading,g=(e,t)=>{if(!e||!t)return!1;let{usedExports:r}=e;return!!r&&!!t.every(e=>r.includes(e))};function y(e,t,r,o){let n=e[t][r],i="",a=u(o),s=function(e,t){return a?!n[e].treeShaking||!!n[t].treeShaking&&!h(n[e].treeShaking)&&p(e,t):!h(n[e])&&p(e,t)};if(a){if(i=f(e[t][r],s))return{version:i,useTreesShaking:a};a=!1}return{version:f(e[t][r],s),useTreesShaking:a}}let b=e=>h(e)||m(e);function E(e,t,r,o){let n=e[t][r],i="",a=u(o),s=function(e,t){if(a){if(!n[e].treeShaking)return!0;if(!n[t].treeShaking)return!1;if(b(n[t].treeShaking))if(b(n[e].treeShaking))return!!p(e,t);else return!0;if(b(n[e].treeShaking))return!1}if(b(n[t]))if(b(n[e]))return!!p(e,t);else return!0;return!b(n[e])&&p(e,t)};if(a){if(i=f(e[t][r],s))return{version:i,useTreesShaking:a};a=!1}return{version:f(e[t][r],s),useTreesShaking:a}}function S(e){return"loaded-first"===e?E:y}function x(e,t,r,n){if(!e)return;let{shareConfig:l,scope:c=a.DEFAULT_SCOPE,strategy:d,treeShaking:p}=r;for(let a of Array.isArray(c)?c:[c])if(l&&e[a]&&e[a][t]){let{requiredVersion:c}=l,{version:f,useTreesShaking:h}=S(d)(e,a,t,p),m=()=>{let n=e[a][t][f];if(l.singleton){if("string"==typeof c&&!s.satisfy(f,c)){let e=`Version ${f} from ${f&&n.from} of shared singleton module ${t} does not satisfy the requirement of ${r.from} which needs ${c})`;l.strictVersion?o.error(e):o.warn(e)}return{shared:n,useTreesShaking:h}}{if(!1===c||"*"===c||s.satisfy(f,c))return{shared:n,useTreesShaking:h};let r=u(p);if(r){for(let[o,n]of Object.entries(e[a][t]))if(u(n.treeShaking,null==p?void 0:p.usedExports)&&s.satisfy(o,c))return{shared:n,useTreesShaking:r}}for(let[r,o]of Object.entries(e[a][t]))if(s.satisfy(r,c))return{shared:o,useTreesShaking:!1}}},g={shareScopeMap:e,scope:a,pkgName:t,version:f,GlobalFederation:i.Global.__FEDERATION__,shareInfo:r,resolver:m};return(n.emit(g)||g).resolver()}}function _(){return i.Global.__FEDERATION__.__SHARE__}function v(e){let{pkgName:t,extraOptions:r,shareInfos:o}=e,n=e=>{if(!e)return;let t={};e.forEach(e=>{t[e.version]=e});let r=function(e,r){return!h(t[e])&&p(e,r)};return t[f(t,r)]},i=(null==r?void 0:r.resolver)??n,a=e=>null!==e&&"object"==typeof e&&!Array.isArray(e),s=function(){for(var e=arguments.length,t=Array(e),r=0;r<e;r++)t[r]=arguments[r];let o={};for(let e of t)if(e)for(let[t,r]of Object.entries(e)){let e=o[t];a(e)&&a(r)?o[t]=s(e,r):void 0!==r&&(o[t]=r)}return o};return s(i(o[t]),null==r?void 0:r.customShareInfo)}function R(e,t){return t&&e.treeShaking?e.treeShaking:e}t.addUseIn=(e,t)=>{e.useIn||(e.useIn=[]),n.addUniqueItem(e.useIn,t)},t.directShare=R,t.formatShareConfigs=d,t.getGlobalShareScope=_,t.getRegisteredShare=x,t.getTargetSharedOptions=v,t.shouldUseTreeShaking=u},8844:function(e,t,r){let o=r(9869),n=r(5652);function i(e,t){return -1===e.findIndex(e=>e===t)&&e.push(t),e}function a(e){return"version"in e&&e.version?`${e.name}:${e.version}`:"entry"in e&&e.entry?`${e.name}:${e.entry}`:`${e.name}`}function s(e){return void 0!==e.entry}function l(e){return!e.entry.includes(".json")}async function c(e,t){try{return await e()}catch(e){t||o.warn(e);return}}function d(e){return e&&"object"==typeof e}let u=Object.prototype.toString;function p(e){return"[object Object]"===u.call(e)}function f(e,t){let r=/^(https?:)?\/\//i;return e.replace(r,"").replace(/\/$/,"")===t.replace(r,"").replace(/\/$/,"")}function h(e){return Array.isArray(e)?e:[e]}function m(e){let t={url:"",type:"global",globalName:""};return n.isBrowserEnvValue||(0,n.isReactNativeEnv)()||!("ssrRemoteEntry"in e)?"remoteEntry"in e?{url:e.remoteEntry,type:e.remoteEntryType,globalName:e.globalName}:t:"ssrRemoteEntry"in e?{url:e.ssrRemoteEntry||t.url,type:e.ssrRemoteEntryType||t.type,globalName:e.globalName}:t}let g=(e,t)=>{let r;return r=e.endsWith("/")?e.slice(0,-1):e,t.startsWith(".")&&(t=t.slice(1)),r+=t};t.addUniqueItem=i,t.arrayOptions=h,t.getFMId=a,t.getRemoteEntryInfoFromSnapshot=m,t.isObject=d,t.isPlainObject=p,t.isPureRemoteEntry=l,t.isRemoteInfoWithEntry=s,t.isStaticResourcesEqual=f,t.objectToString=u,t.processModuleAlias=g,t.safeWrapper=c},6714:function(e,t){var r=Object.create,o=Object.defineProperty,n=Object.getOwnPropertyDescriptor,i=Object.getOwnPropertyNames,a=Object.getPrototypeOf,s=Object.prototype.hasOwnProperty,l=(e,t,r,a)=>{if(t&&"object"==typeof t||"function"==typeof t)for(var l,c=i(t),d=0,u=c.length;d<u;d++)l=c[d],s.call(e,l)||l===r||o(e,l,{get:(e=>t[e]).bind(null,l),enumerable:!(a=n(t,l))||a.enumerable});return e};t.__toESM=(e,t,n)=>(n=null!=e?r(a(e)):{},l(!t&&e&&e.__esModule?n:o(n,"default",{value:e,enumerable:!0}),e))},4043:function(e,t,r){Object.defineProperties(t,{__esModule:{value:!0},[Symbol.toStringTag]:{value:"Module"}}),r(6714);let o=r(9695),n=r(4328),i={...n.helpers.global,getGlobalFederationInstance:o.getGlobalFederationInstance},a=n.helpers.share,s=n.helpers.utils;t.default={global:i,share:a,utils:s},t.global=i,t.share=a,t.utils=s},132:function(e,t,r){Object.defineProperty(t,Symbol.toStringTag,{value:"Module"}),r(6714);let o=r(9695),n=r(4328),i=r(8933);function a(e){let t=new((0,n.getGlobalFederationConstructor)()||n.ModuleFederation)({id:`${e.name}@${e.version||Date.now()}`,...e});return(0,n.setGlobalFederationInstance)(t),t}let s=null;function l(e){let t=o.getGlobalFederationInstance(e.name,e.version),r={...e,id:e.id||""};return t?(t.initOptions(r),s||(s=t),t):s=a(r)}function c(){for(var e=arguments.length,t=Array(e),r=0;r<e;r++)t[r]=arguments[r];return(0,n.assert)(s,i.RUNTIME_009,i.runtimeDescMap),s.loadRemote.apply(s,t)}function d(){for(var e=arguments.length,t=Array(e),r=0;r<e;r++)t[r]=arguments[r];return(0,n.assert)(s,i.RUNTIME_009,i.runtimeDescMap),s.loadShare.apply(s,t)}function u(){for(var e=arguments.length,t=Array(e),r=0;r<e;r++)t[r]=arguments[r];return(0,n.assert)(s,i.RUNTIME_009,i.runtimeDescMap),s.loadShareSync.apply(s,t)}function p(){for(var e=arguments.length,t=Array(e),r=0;r<e;r++)t[r]=arguments[r];return(0,n.assert)(s,i.RUNTIME_009,i.runtimeDescMap),s.preloadRemote.apply(s,t)}function f(){for(var e=arguments.length,t=Array(e),r=0;r<e;r++)t[r]=arguments[r];return(0,n.assert)(s,i.RUNTIME_009,i.runtimeDescMap),s.registerRemotes.apply(s,t)}function h(){for(var e=arguments.length,t=Array(e),r=0;r<e;r++)t[r]=arguments[r];return(0,n.assert)(s,i.RUNTIME_009,i.runtimeDescMap),s.registerPlugins.apply(s,t)}function m(e){return e?n.CurrentGlobal.__FEDERATION__.__INSTANCES__.find(e)||null:s}function g(){for(var e=arguments.length,t=Array(e),r=0;r<e;r++)t[r]=arguments[r];return(0,n.assert)(s,i.RUNTIME_009,i.runtimeDescMap),s.registerShared.apply(s,t)}(0,n.setGlobalFederationConstructor)(n.ModuleFederation),Object.defineProperty(t,"Module",{enumerable:!0,get:function(){return n.Module}}),t.ModuleFederation=n.ModuleFederation,t.createInstance=a,t.getInstance=m,Object.defineProperty(t,"getRemoteEntry",{enumerable:!0,get:function(){return n.getRemoteEntry}}),Object.defineProperty(t,"getRemoteInfo",{enumerable:!0,get:function(){return n.getRemoteInfo}}),t.init=l,t.loadRemote=c,Object.defineProperty(t,"loadScript",{enumerable:!0,get:function(){return n.loadScript}}),Object.defineProperty(t,"loadScriptNode",{enumerable:!0,get:function(){return n.loadScriptNode}}),t.loadShare=d,t.loadShareSync=u,t.preloadRemote=p,Object.defineProperty(t,"registerGlobalPlugins",{enumerable:!0,get:function(){return n.registerGlobalPlugins}}),t.registerPlugins=h,t.registerRemotes=f,t.registerShared=g},9695:function(e,t,r){r(6714);let o=r(4328);function n(){return"coreshoporder:1.0.0"}t.getGlobalFederationInstance=function(e,t){let r=n();return o.CurrentGlobal.__FEDERATION__.__INSTANCES__.find(o=>!!r&&o.options.id===r||o.options.name===e&&!o.options.version&&!t||o.options.name===e&&!!t&&o.options.version===t)}},2618:function(e,t){var r=Object.defineProperty;t.__exportAll=(e,t)=>{let o={};for(var n in e)r(o,n,{get:e[n],enumerable:!0});return t||r(o,Symbol.toStringTag,{value:"Module"}),o}},9676:function(e,t){let r="federation-manifest.json",o=".json",n="FEDERATION_DEBUG",i={AT:"@",HYPHEN:"-",SLASH:"/"},a={[i.AT]:"scope_",[i.HYPHEN]:"_",[i.SLASH]:"__"},s={[a[i.AT]]:i.AT,[a[i.HYPHEN]]:i.HYPHEN,[a[i.SLASH]]:i.SLASH},l=":",c="mf-manifest.json",d="mf-stats.json",u={NPM:"npm",APP:"app"},p="__MF_DEVTOOLS_MODULE_INFO__",f="ENCODE_NAME_PREFIX",h=".federation",m=function(e){return e[e.UNKNOWN=1]="UNKNOWN",e[e.CALCULATED=2]="CALCULATED",e[e.NO_USE=0]="NO_USE",e}({});t.BROWSER_LOG_KEY=n,t.ENCODE_NAME_PREFIX=f,t.EncodedNameTransformMap=s,t.FederationModuleManifest=r,t.MANIFEST_EXT=o,t.MFModuleType=u,t.MODULE_DEVTOOL_IDENTIFIER=p,t.ManifestFileName=c,t.NameTransformMap=a,t.NameTransformSymbol=i,t.SEPARATOR=l,t.StatsFileName=d,t.TEMP_DIR=h,t.TreeShakingStatus=m},1905:function(e,t){t.createModuleFederationConfig=e=>e},708:function(e,t,r){let o=r(5455);async function n(e,t){try{return await e()}catch(e){t||o.warn(e);return}}function i(e,t){let r=/^(https?:)?\/\//i;return e.replace(r,"").replace(/\/$/,"")===t.replace(r,"").replace(/\/$/,"")}function a(e){let t,r=null,o=!0,a=2e4,s=document.getElementsByTagName("script");for(let t=0;t<s.length;t++){let n=s[t],a=n.getAttribute("src");if(a&&i(a,e.url)){r=n,o=!1;break}}if(!r){let t,o=e.attrs;(r=document.createElement("script")).type=(null==o?void 0:o.type)==="module"?"module":"text/javascript",e.createScriptHook&&((t=e.createScriptHook(e.url,e.attrs))instanceof HTMLScriptElement?r=t:"object"==typeof t&&("script"in t&&t.script&&(r=t.script),"timeout"in t&&t.timeout&&(a=t.timeout))),r.src||(r.src=e.url),o&&!t&&Object.keys(o).forEach(e=>{r&&("async"===e||"defer"===e?r[e]=o[e]:r.getAttribute(e)||r.setAttribute(e,o[e]))})}let l=null,c="undefined"!=typeof window?t=>{if(t.filename&&i(t.filename,e.url)){let r=Error(`ScriptExecutionError: Script "${e.url}" loaded but threw a runtime error during execution: ${t.message} (${t.filename}:${t.lineno}:${t.colno})`);r.name="ScriptExecutionError",l=r}}:null;c&&window.addEventListener("error",c);let d=async(o,i)=>{clearTimeout(t),c&&window.removeEventListener("error",c);let a=()=>{if((null==i?void 0:i.type)==="error"){let t=Error((null==i?void 0:i.isTimeout)?`ScriptNetworkError: Script "${e.url}" timed out.`:`ScriptNetworkError: Failed to load script "${e.url}" - the script URL is unreachable or the server returned an error (network failure, 404, CORS, etc.)`);t.name="ScriptNetworkError",(null==e?void 0:e.onErrorCallback)&&(null==e||e.onErrorCallback(t))}else l?(null==e?void 0:e.onErrorCallback)&&(null==e||e.onErrorCallback(l)):(null==e?void 0:e.cb)&&(null==e||e.cb())};if(r&&(r.onerror=null,r.onload=null,n(()=>{let{needDeleteScript:t=!0}=e;t&&(null==r?void 0:r.parentNode)&&r.parentNode.removeChild(r)}),o&&"function"==typeof o)){let e=o(i);if(e instanceof Promise){let t=await e;return a(),t}return a(),e}a()};return r.onerror=d.bind(null,r.onerror),r.onload=d.bind(null,r.onload),t=setTimeout(()=>{d(null,{type:"error",isTimeout:!0})},a),{script:r,needAttach:o}}function s(e,t){let{attrs:r={},createScriptHook:o}=t;return new Promise((t,n)=>{let{script:i,needAttach:s}=a({url:e,cb:t,onErrorCallback:n,attrs:{fetchpriority:"high",...r},createScriptHook:o,needDeleteScript:!0});s&&document.head.appendChild(i)})}t.createLink=function(e){let t,r=null,o=!0,a=2e4,s=document.getElementsByTagName("link");for(let t=0;t<s.length;t++){let n=s[t],a=n.getAttribute("href"),l=n.getAttribute("rel");if(a&&i(a,e.url)&&l===e.attrs.rel){r=n,o=!1;break}}if(!r){let t;(r=document.createElement("link")).setAttribute("href",e.url);let o=!0,n=e.attrs;e.createLinkHook&&((t=e.createLinkHook(e.url,n))instanceof HTMLLinkElement?(r=t,o=!1):"object"==typeof t&&("link"in t&&t.link&&(r=t.link,o=!1),"timeout"in t&&t.timeout&&(a=t.timeout))),n&&o&&Object.keys(n).forEach(e=>{r&&!r.getAttribute(e)&&r.setAttribute(e,n[e])})}if(!o)return Promise.resolve().then(()=>{(null==e?void 0:e.cb)&&(null==e||e.cb())}),{link:r,needAttach:o};let l=(o,i)=>{t&&clearTimeout(t);let a=()=>{if((null==i?void 0:i.type)==="error"){let t=Error((null==i?void 0:i.isTimeout)?`LinkNetworkError: Link "${e.url}" timed out.`:`LinkNetworkError: Failed to load link "${e.url}" - the URL is unreachable or the server returned an error.`);t.name="LinkNetworkError",(null==e?void 0:e.onErrorCallback)&&(null==e||e.onErrorCallback(t))}else(null==e?void 0:e.cb)&&(null==e||e.cb())};if(r&&(r.onerror=null,r.onload=null,n(()=>{let{needDeleteLink:t=!0}=e;t&&(null==r?void 0:r.parentNode)&&r.parentNode.removeChild(r)}),o)){let e=o(i);return a(),e}a()};return r.onerror=l.bind(null,r.onerror),r.onload=l.bind(null,r.onload),t=setTimeout(()=>{l(null,{type:"error",isTimeout:!0})},a),{link:r,needAttach:o}},t.createScript=a,t.isStaticResourcesEqual=i,t.loadScript=s,t.safeWrapper=n},3549:function(e,t,r){let o=r(9676),n="undefined"!=typeof ENV_TARGET?"web"===ENV_TARGET:"undefined"!=typeof window&&void 0!==window.document;function i(){return n}function a(){var e;return"undefined"!=typeof navigator&&(null==(e=navigator)?void 0:e.product)==="ReactNative"}function s(){try{if(i()&&window.localStorage)return!!localStorage.getItem(o.BROWSER_LOG_KEY)}catch(e){}return!1}function l(){return"undefined"!=typeof process&&process.env&&process.env.FEDERATION_DEBUG?!!process.env.FEDERATION_DEBUG:!!("undefined"!=typeof FEDERATION_DEBUG&&FEDERATION_DEBUG)||s()}t.getProcessEnv=function(){return"undefined"!=typeof process&&process.env?process.env:{}},t.isBrowserEnv=i,t.isBrowserEnvValue=n,t.isDebugMode=l,t.isReactNativeEnv=a},3426:function(e,t,r){let o=r(9676),n=(e,t)=>{if(!e)return t;let r=(e=>{if("."===e)return"";if(e.startsWith("./"))return e.replace("./","");if(e.startsWith("/")){let t=e.slice(1);return t.endsWith("/")?t.slice(0,-1):t}return e})(e);return r?r.endsWith("/")?`${r}${t}`:`${r}/${t}`:t};function i(e){return e.replace(/#.*$/,"").replace(/\?.*$/,"").replace(/\/[^\/]+$/,"/")}function a(e){return!!("remoteEntry"in e&&e.remoteEntry.includes(o.MANIFEST_EXT))}function s(e){if(!e)return{statsFileName:o.StatsFileName,manifestFileName:o.ManifestFileName};let t="boolean"==typeof e?"":e.filePath||"",r="boolean"==typeof e?"":e.fileName||"",i=".json",a=e=>e.endsWith(i)?e:`${e}${i}`,s=(e,t)=>e.replace(i,`${t}${i}`),l=r?a(r):o.ManifestFileName;return{statsFileName:n(t,r?s(l,"-stats"):o.StatsFileName),manifestFileName:n(t,l)}}t.generateSnapshotFromManifest=function(e){var t;let r,o=arguments.length>1&&void 0!==arguments[1]?arguments[1]:{},{remotes:a={},overrides:s={},version:l}=o,c=()=>"publicPath"in e.metaData?("auto"===e.metaData.publicPath||""===e.metaData.publicPath)&&l?i(l):e.metaData.publicPath:e.metaData.getPublicPath,d=Object.keys(s),u={};Object.keys(a).length||(u=(null==(t=e.remotes)?void 0:t.reduce((e,t)=>{let r,o=t.federationContainerName;return r=d.includes(o)?s[o]:"version"in t?t.version:t.entry,e[o]={matchedVersion:r},e},{}))||{}),Object.keys(a).forEach(e=>u[e]={matchedVersion:d.includes(e)?s[e]:a[e]});let{remoteEntry:{path:p,name:f,type:h},types:m={path:"",name:"",zip:"",api:""},buildInfo:{buildVersion:g},globalName:y,ssrRemoteEntry:b}=e.metaData,{exposes:E}=e,S={version:l||"",buildVersion:g,globalName:y,remoteEntry:n(p,f),remoteEntryType:h,remoteTypes:n(m.path,m.name),remoteTypesZip:m.zip||"",remoteTypesAPI:m.api||"",remotesInfo:u,shared:null==e?void 0:e.shared.map(e=>({assets:e.assets,sharedName:e.name,version:e.version,usedExports:e.referenceExports||[]})),modules:null==E?void 0:E.map(e=>({moduleName:e.name,modulePath:e.path,assets:e.assets}))};if("publicPath"in e.metaData?(r={...S,publicPath:c()},"string"==typeof e.metaData.ssrPublicPath&&(r.ssrPublicPath=e.metaData.ssrPublicPath)):r={...S,getPublicPath:c()},b){let e=n(b.path,b.name);r.ssrRemoteEntry=e,r.ssrRemoteEntryType=b.type||"commonjs-module"}return r},t.getManifestFileName=s,t.inferAutoPublicPath=i,t.isManifestProvider=a,t.simpleJoinRemoteEntry=n},5652:function(e,t,r){Object.defineProperty(t,Symbol.toStringTag,{value:"Module"});let o=r(9676),n=r(1551),i=r(9828),a=r(5663),s=r(8299),l=r(6747),c=r(4198),d=r(3549),u=r(5455),p=r(3426),f=r(8508),h=r(708),m=r(9412),g=r(9405),y=r(1905);t.BROWSER_LOG_KEY=o.BROWSER_LOG_KEY,t.ENCODE_NAME_PREFIX=o.ENCODE_NAME_PREFIX,t.EncodedNameTransformMap=o.EncodedNameTransformMap,t.FederationModuleManifest=o.FederationModuleManifest,t.MANIFEST_EXT=o.MANIFEST_EXT,t.MFModuleType=o.MFModuleType,t.MODULE_DEVTOOL_IDENTIFIER=o.MODULE_DEVTOOL_IDENTIFIER,t.ManifestFileName=o.ManifestFileName,t.NameTransformMap=o.NameTransformMap,t.NameTransformSymbol=o.NameTransformSymbol,t.SEPARATOR=o.SEPARATOR,t.StatsFileName=o.StatsFileName,t.TEMP_DIR=o.TEMP_DIR,t.TreeShakingStatus=o.TreeShakingStatus,t.assert=u.assert,t.bindLoggerToCompiler=f.bindLoggerToCompiler,t.composeKeyWithSeparator=u.composeKeyWithSeparator,Object.defineProperty(t,"consumeSharedPlugin",{enumerable:!0,get:function(){return l.ConsumeSharedPlugin_exports}}),Object.defineProperty(t,"containerPlugin",{enumerable:!0,get:function(){return n.ContainerPlugin_exports}}),Object.defineProperty(t,"containerReferencePlugin",{enumerable:!0,get:function(){return i.ContainerReferencePlugin_exports}}),t.createInfrastructureLogger=f.createInfrastructureLogger,t.createLink=h.createLink,t.createLogger=f.createLogger,t.createModuleFederationConfig=y.createModuleFederationConfig,t.createScript=h.createScript,t.createScriptNode=m.createScriptNode,t.decodeName=u.decodeName,t.encodeName=u.encodeName,t.error=u.error,t.generateExposeFilename=u.generateExposeFilename,t.generateShareFilename=u.generateShareFilename,t.generateSnapshotFromManifest=p.generateSnapshotFromManifest,t.getManifestFileName=p.getManifestFileName,t.getProcessEnv=d.getProcessEnv,t.getResourceUrl=u.getResourceUrl,t.inferAutoPublicPath=p.inferAutoPublicPath,t.infrastructureLogger=f.infrastructureLogger,t.isBrowserEnv=d.isBrowserEnv,t.isBrowserEnvValue=d.isBrowserEnvValue,t.isDebugMode=d.isDebugMode,t.isManifestProvider=p.isManifestProvider,t.isReactNativeEnv=d.isReactNativeEnv,t.isRequiredVersion=u.isRequiredVersion,t.isStaticResourcesEqual=h.isStaticResourcesEqual,t.loadScript=h.loadScript,t.loadScriptNode=m.loadScriptNode,t.logger=f.logger,Object.defineProperty(t,"moduleFederationPlugin",{enumerable:!0,get:function(){return a.ModuleFederationPlugin_exports}}),t.normalizeOptions=g.normalizeOptions,t.parseEntry=u.parseEntry,Object.defineProperty(t,"provideSharedPlugin",{enumerable:!0,get:function(){return c.ProvideSharedPlugin_exports}}),t.safeToString=u.safeToString,t.safeWrapper=h.safeWrapper,Object.defineProperty(t,"sharePlugin",{enumerable:!0,get:function(){return s.SharePlugin_exports}}),t.simpleJoinRemoteEntry=p.simpleJoinRemoteEntry,t.warn=u.warn},8508:function(e,t,r){let o=r(3549),n="[ Module Federation ]",i=console,a=["logger.ts","logger.js","captureStackTrace","Logger.emit","Logger.log","Logger.info","Logger.warn","Logger.error","Logger.debug"];function s(){try{let e=Error().stack;if(!e)return;let[,...t]=e.split("\n"),r=t.filter(e=>!a.some(t=>e.includes(t)));if(!r.length)return;return`Stack trace:
${r.slice(0,5).join("\n")}`}catch{return}}var l=class{setPrefix(e){this.prefix=e}setDelegate(e){this.delegate=e??i}emit(e,t){let r=this.delegate,n=o.isDebugMode()?s():void 0,a=n?[...t,n]:t,l=(()=>{switch(e){case"log":return["log","info"];case"info":return["info","log"];case"warn":return["warn","info","log"];case"error":return["error","warn","log"];default:return["debug","log"]}})();for(let e of l){let t=r[e];if("function"==typeof t)return void t.call(r,this.prefix,...a)}for(let e of l){let t=i[e];if("function"==typeof t)return void t.call(i,this.prefix,...a)}}log(){for(var e=arguments.length,t=Array(e),r=0;r<e;r++)t[r]=arguments[r];this.emit("log",t)}warn(){for(var e=arguments.length,t=Array(e),r=0;r<e;r++)t[r]=arguments[r];this.emit("warn",t)}error(){for(var e=arguments.length,t=Array(e),r=0;r<e;r++)t[r]=arguments[r];this.emit("error",t)}success(){for(var e=arguments.length,t=Array(e),r=0;r<e;r++)t[r]=arguments[r];this.emit("info",t)}info(){for(var e=arguments.length,t=Array(e),r=0;r<e;r++)t[r]=arguments[r];this.emit("info",t)}ready(){for(var e=arguments.length,t=Array(e),r=0;r<e;r++)t[r]=arguments[r];this.emit("info",t)}debug(){for(var e=arguments.length,t=Array(e),r=0;r<e;r++)t[r]=arguments[r];o.isDebugMode()&&this.emit("debug",t)}constructor(e,t=i){this.prefix=e,this.delegate=t??i}};function c(e){return new l(e)}function d(e){let t=new l(e);return Object.defineProperty(t,"__mf_infrastructure_logger__",{value:!0,enumerable:!1,configurable:!1}),t}function u(e,t,r){if(e.__mf_infrastructure_logger__&&(null==t?void 0:t.getInfrastructureLogger))try{let o=t.getInfrastructureLogger(r);o&&"object"==typeof o&&("function"==typeof o.log||"function"==typeof o.info||"function"==typeof o.warn||"function"==typeof o.error)&&e.setDelegate(o)}catch{e.setDelegate(void 0)}}let p=c(n),f=d(n);t.bindLoggerToCompiler=u,t.createInfrastructureLogger=d,t.createLogger=c,t.infrastructureLogger=f,t.logger=p},9412:function(__unused_webpack_module,exports){let sdkImportCache=new Map;function importNodeModule(e){if(!e)throw Error("import specifier is required");if(sdkImportCache.has(e))return sdkImportCache.get(e);let t=Function("name","return import(name)")(e).then(e=>e).catch(t=>{throw console.error(`Error importing module ${e}:`,t),sdkImportCache.delete(e),t});return sdkImportCache.set(e,t),t}let loadNodeFetch=async()=>{let e=await importNodeModule("node-fetch");return e.default||e},lazyLoaderHookFetch=async(e,t,r)=>{let o=(e,t)=>r.lifecycle.fetch.emit(e,t),n=await o(e,t||{});return n&&n instanceof Response?n:("undefined"==typeof fetch?await loadNodeFetch():fetch)(e,t||{})},createScriptNode="undefined"==typeof ENV_TARGET||"web"!==ENV_TARGET?(url,cb,attrs,loaderHook)=>{let urlObj;if(null==loaderHook?void 0:loaderHook.createScriptHook){let hookResult=loaderHook.createScriptHook(url);hookResult&&"object"==typeof hookResult&&"url"in hookResult&&(url=hookResult.url)}try{urlObj=new URL(url)}catch(e){console.error("Error constructing URL:",e),cb(Error(`Invalid URL: ${e}`));return}let getFetch=async()=>(null==loaderHook?void 0:loaderHook.fetch)?(e,t)=>lazyLoaderHookFetch(e,t,loaderHook):"undefined"==typeof fetch?loadNodeFetch():fetch,handleScriptFetch=async(f,urlObj)=>{try{var _vm_constants;let requireFn,res=await f(urlObj.href),data=await res.text(),[path,vm]=await Promise.all([importNodeModule("path"),importNodeModule("vm")]),scriptContext={exports:{},module:{exports:{}}},urlDirname=urlObj.pathname.split("/").slice(0,-1).join("/"),filename=path.basename(urlObj.pathname),script=new vm.Script(`(function(exports, module, require, __dirname, __filename) {${data}
})`,{filename,importModuleDynamically:(null==(_vm_constants=vm.constants)?void 0:_vm_constants.USE_MAIN_CONTEXT_DEFAULT_LOADER)??importNodeModule});requireFn=eval("require"),script.runInThisContext()(scriptContext.exports,scriptContext.module,requireFn,urlDirname,filename);let exportedInterface=scriptContext.module.exports||scriptContext.exports;if(attrs&&exportedInterface&&attrs.globalName)return void cb(void 0,exportedInterface[attrs.globalName]||exportedInterface);cb(void 0,exportedInterface)}catch(e){cb(e instanceof Error?e:Error(`Script execution error: ${e}`))}};getFetch().then(async e=>{if((null==attrs?void 0:attrs.type)==="esm"||(null==attrs?void 0:attrs.type)==="module")return loadModule(urlObj.href,{fetch:e,vm:await importNodeModule("vm")}).then(async e=>{await e.evaluate(),cb(void 0,e.namespace)}).catch(e=>{cb(e instanceof Error?e:Error(`Script execution error: ${e}`))});handleScriptFetch(e,urlObj)}).catch(e=>{cb(e)})}:(e,t,r,o)=>{t(Error("createScriptNode is disabled in non-Node.js environment"))},loadScriptNode="undefined"==typeof ENV_TARGET||"web"!==ENV_TARGET?(e,t)=>new Promise((r,o)=>{createScriptNode(e,(e,n)=>{if(e)o(e);else{var i,a;let e=(null==t||null==(i=t.attrs)?void 0:i.globalName)||`__FEDERATION_${null==t||null==(a=t.attrs)?void 0:a.name}:custom__`;r(globalThis[e]=n)}},t.attrs,t.loaderHook)}):(e,t)=>{throw Error("loadScriptNode is disabled in non-Node.js environment")},esmModuleCache=new Map;async function loadModule(e,t){if(esmModuleCache.has(e))return esmModuleCache.get(e);let{fetch:r,vm:o}=t,n=await (await r(e)).text(),i=new o.SourceTextModule(n,{importModuleDynamically:async(r,o)=>loadModule(new URL(r,e).href,t)});return esmModuleCache.set(e,i),await i.link(async r=>{let o=new URL(r,e).href;return await loadModule(o,t)}),i}exports.createScriptNode=createScriptNode,exports.loadScriptNode=loadScriptNode},9405:function(e,t){t.normalizeOptions=function(e,t,r){return function(o){if(!1===o)return!1;if(void 0===o)if(e)return t;else return!1;if(!0===o)return t;if(o&&"object"==typeof o)return{...t,...o};throw Error(`Unexpected type for \`${r}\`, expect boolean/undefined/object, got: ${typeof o}`)}}},6747:function(e,t,r){var o=r(2618).__exportAll({});Object.defineProperty(t,"ConsumeSharedPlugin_exports",{enumerable:!0,get:function(){return o}})},1551:function(e,t,r){var o=r(2618).__exportAll({});Object.defineProperty(t,"ContainerPlugin_exports",{enumerable:!0,get:function(){return o}})},9828:function(e,t,r){var o=r(2618).__exportAll({});Object.defineProperty(t,"ContainerReferencePlugin_exports",{enumerable:!0,get:function(){return o}})},5663:function(e,t,r){var o=r(2618).__exportAll({});Object.defineProperty(t,"ModuleFederationPlugin_exports",{enumerable:!0,get:function(){return o}})},4198:function(e,t,r){var o=r(2618).__exportAll({});Object.defineProperty(t,"ProvideSharedPlugin_exports",{enumerable:!0,get:function(){return o}})},8299:function(e,t,r){var o=r(2618).__exportAll({});Object.defineProperty(t,"SharePlugin_exports",{enumerable:!0,get:function(){return o}})},5455:function(e,t,r){let o=r(9676),n=r(3549),i="[ Federation Runtime ]",a=function(e,t){let r=arguments.length>2&&void 0!==arguments[2]?arguments[2]:o.SEPARATOR,i=e.split(r),a="development"===n.getProcessEnv().NODE_ENV&&t,s="*",l=e=>e.startsWith("http")||e.includes(o.MANIFEST_EXT);if(i.length>=2){let[t,...o]=i;e.startsWith(r)&&(t=i.slice(0,2).join(r),o=[a||i.slice(2).join(r)]);let n=a||o.join(r);return l(n)?{name:t,entry:n}:{name:t,version:n||s}}if(1===i.length){let[e]=i;return a&&l(a)?{name:e,entry:a}:{name:e,version:a||s}}throw`Invalid entry value: ${e}`},s=function(){for(var e=arguments.length,t=Array(e),r=0;r<e;r++)t[r]=arguments[r];return t.length?t.reduce((e,t)=>t?e?`${e}${o.SEPARATOR}${t}`:t:e,""):""},l=function(e){let t=arguments.length>1&&void 0!==arguments[1]?arguments[1]:"",r=arguments.length>2&&void 0!==arguments[2]&&arguments[2];try{let n=r?".js":"";return`${t}${e.replace(RegExp(`${o.NameTransformSymbol.AT}`,"g"),o.NameTransformMap[o.NameTransformSymbol.AT]).replace(RegExp(`${o.NameTransformSymbol.HYPHEN}`,"g"),o.NameTransformMap[o.NameTransformSymbol.HYPHEN]).replace(RegExp(`${o.NameTransformSymbol.SLASH}`,"g"),o.NameTransformMap[o.NameTransformSymbol.SLASH])}${n}`}catch(e){throw e}},c=function(e,t,r){try{let n=e;if(t){if(!n.startsWith(t))return n;n=n.replace(RegExp(t,"g"),"")}return n=n.replace(RegExp(`${o.NameTransformMap[o.NameTransformSymbol.AT]}`,"g"),o.EncodedNameTransformMap[o.NameTransformMap[o.NameTransformSymbol.AT]]).replace(RegExp(`${o.NameTransformMap[o.NameTransformSymbol.SLASH]}`,"g"),o.EncodedNameTransformMap[o.NameTransformMap[o.NameTransformSymbol.SLASH]]).replace(RegExp(`${o.NameTransformMap[o.NameTransformSymbol.HYPHEN]}`,"g"),o.EncodedNameTransformMap[o.NameTransformMap[o.NameTransformSymbol.HYPHEN]]),r&&(n=n.replace(".js","")),n}catch(e){throw e}},d=(e,t)=>{if(!e)return"";let r=e;return"."===r&&(r="default_export"),r.startsWith("./")&&(r=r.replace("./","")),l(r,"__federation_expose_",t)},u=(e,t)=>e?l(e,"__federation_shared_",t):"",p=(e,t)=>{if("getPublicPath"in e){let r;return r=e.getPublicPath.startsWith("function")?Function("return "+e.getPublicPath)()():Function(e.getPublicPath)(),`${r}${t}`}return"publicPath"in e?!n.isBrowserEnv()&&!n.isReactNativeEnv()&&"ssrPublicPath"in e&&"string"==typeof e.ssrPublicPath?`${e.ssrPublicPath}${t}`:`${e.publicPath}${t}`:(console.warn("Cannot get resource URL. If in debug mode, please ignore.",e,t),"")},f=e=>{throw Error(`${i}: ${e}`)},h=e=>{console.warn(`${i}: ${e}`)};function m(e){try{return JSON.stringify(e,null,2)}catch(e){return""}}let g=/^([\d^=v<>~]|[*xX]$)/;function y(e){return g.test(e)}t.assert=(e,t)=>{e||f(t)},t.composeKeyWithSeparator=s,t.decodeName=c,t.encodeName=l,t.error=f,t.generateExposeFilename=d,t.generateShareFilename=u,t.getResourceUrl=p,t.isRequiredVersion=y,t.parseEntry=a,t.safeToString=m,t.warn=h},3473:function(e,t){var r=Object.create,o=Object.defineProperty,n=Object.getOwnPropertyDescriptor,i=Object.getOwnPropertyNames,a=Object.getPrototypeOf,s=Object.prototype.hasOwnProperty,l=(e,t,r,a)=>{if(t&&"object"==typeof t||"function"==typeof t)for(var l,c=i(t),d=0,u=c.length;d<u;d++)l=c[d],s.call(e,l)||l===r||o(e,l,{get:(e=>t[e]).bind(null,l),enumerable:!(a=n(t,l))||a.enumerable});return e};t.__toESM=(e,t,n)=>(n=null!=e?r(a(e)):{},l(!t&&e&&e.__esModule?n:o(n,"default",{value:e,enumerable:!0}),e))},6235:function(e,t){t.attachShareScopeMap=function(e){e.S&&!e.federation.hasAttachShareScopeMap&&e.federation.instance&&e.federation.instance.shareScopeMap&&(e.S=e.federation.instance.shareScopeMap,e.federation.hasAttachShareScopeMap=!0)}},4260:function(e,t){Object.defineProperty(t,Symbol.toStringTag,{value:"Module"}),t.FEDERATION_SUPPORTED_TYPES=["script"]},9290:function(e,t,r){let o=r(6235),n=r(2910),i=r(5543),a=r(1143),s=r(5719);t.consumes=function(e){n.updateConsumeOptions(e);let{chunkId:t,promises:r,installedModules:l,webpackRequire:c,chunkMapping:d,moduleToHandlerMapping:u}=e;o.attachShareScopeMap(c),c.o(d,t)&&d[t].forEach(e=>{if(c.o(l,e))return r.push(l[e]);let t=t=>{l[e]=0,c.m[e]=r=>{var o;delete c.c[e];let n=t(),{shareInfo:i}=u[e];if((null==i||null==(o=i.shareConfig)?void 0:o.layer)&&n&&"object"==typeof n)try{n.hasOwnProperty("layer")&&void 0!==n.layer||(n.layer=i.shareConfig.layer)}catch(e){}r.exports=n}},o=t=>{delete l[e],c.m[e]=r=>{throw delete c.c[e],t}};try{let n=c.federation.instance;if(!n)throw Error("Federation instance not found!");let{shareKey:d,getter:p,shareInfo:f,treeShakingGetter:h}=u[e],m=i.getUsedExports(c,d),g={...f};Array.isArray(g.scope)&&Array.isArray(g.scope[0])&&(g.scope=g.scope[0]),m&&(g.treeShaking={usedExports:m,useIn:[n.options.name]});let y=n.loadShare(d,{customShareInfo:g}).then(e=>{if(!1===e){if("function"!=typeof p)throw Error(s.getShortErrorMsg(a.RUNTIME_012,{[a.RUNTIME_012]:'The getter for the shared module is not a function. This may be caused by setting "shared.import: false" without the host providing the corresponding lib.'},{shareKey:d}));return(null==h?void 0:h())||p()}return e});y.then?r.push(l[e]=y.then(t).catch(o)):t(y)}catch(e){o(e)}})}},1143:function(e,t){t.RUNTIME_012="RUNTIME-012"},5719:function(e,t){let r=e=>`View the docs to see how to solve: https://module-federation.io/guide/troubleshooting/${e.split("-")[0].toLowerCase()}#${e.toLowerCase()}`;t.getShortErrorMsg=(e,t,o,n)=>{let i=[`${[t[e]]} #${e}`];return o&&i.push(`args: ${JSON.stringify(o)}`),i.push(r(e)),n&&i.push(`Original Error Message:
 ${n}`),i.join("\n")}},2025:function(e,t){t.getSharedFallbackGetter=e=>{let{shareKey:t,factory:r,version:o,webpackRequire:n,libraryType:i="global"}=e,{runtime:a,instance:s,bundlerRuntime:l,sharedFallback:c}=n.federation;if(!c)return r;let d=c[t];if(!d)return r;let u=o?d.find(e=>e[1]===o):d[0];if(!u)throw Error(`No fallback item found for shareKey: ${t} and version: ${o}`);return()=>a.getRemoteEntry({origin:n.federation.instance,remoteInfo:{name:u[2],entry:`${n.p}${u[0]}`,type:i,entryGlobalName:u[2],shareScope:"default"}}).then(e=>{if(!e)throw Error(`Failed to load fallback entry for shareKey: ${t} and version: ${o}`);return e.init(n.federation.instance,l).then(()=>e.get())})}},5543:function(e,t){t.getUsedExports=function(e,t){let r=e.federation.usedExports;if(r)return r[t]}},281:function(e,t,r){Object.defineProperties(t,{__esModule:{value:!0},[Symbol.toStringTag]:{value:"Module"}});let o=r(3473),n=r(6235),i=r(3992),a=r(9290),s=r(6103),l=r(1657),c=r(4298),d=r(2097),u=r(2025),p=r(132),f={runtime:p=o.__toESM(p),instance:void 0,initOptions:void 0,bundlerRuntime:{remotes:i.remotes,consumes:a.consumes,I:s.initializeSharing,S:{},installInitialConsumes:l.installInitialConsumes,initContainerEntry:c.initContainerEntry,init:d.init,getSharedFallbackGetter:u.getSharedFallbackGetter},attachShareScopeMap:n.attachShareScopeMap,bundlerRuntimeOptions:{}},h=f.instance,m=f.initOptions,g=f.bundlerRuntime,y=f.bundlerRuntimeOptions;t.attachShareScopeMap=n.attachShareScopeMap,t.bundlerRuntime=g,t.bundlerRuntimeOptions=y,t.default=f,t.initOptions=m,t.instance=h,Object.defineProperty(t,"runtime",{enumerable:!0,get:function(){return p}})},2097:function(e,t,r){let o=r(3473),n=r(132),i=r(4043);i=o.__toESM(i),t.init=function(e){var t;let{webpackRequire:o}=e,{initOptions:a,runtime:s,sharedFallback:l,bundlerRuntime:c,libraryType:d}=o.federation;if(!a)throw Error("initOptions is required!");let u=function(){return{name:"tree-shake-plugin",beforeInit(e){let{userOptions:t,origin:a,options:s}=e,u=t.version||s.version;if(!l)return e;let p=t.shared||{},f=[];Object.keys(p).forEach(e=>{(Array.isArray(p[e])?p[e]:[p[e]]).forEach(t=>{if(f.push([e,t]),"get"in t){var r;(r=t).treeShaking||(r.treeShaking={}),t.treeShaking.get=t.get,t.get=c.getSharedFallbackGetter({shareKey:e,factory:t.get,webpackRequire:o,libraryType:d,version:t.version})}})});let h=i.default.global.getGlobalSnapshotInfoByModuleInfo({name:a.name,version:u});if(!h||!("shared"in h))return e;Object.keys(s.shared||{}).forEach(e=>{s.shared[e].forEach(t=>{f.push([e,t])})});let m=(e,t)=>{let o=h.shared.find(t=>t.sharedName===e);if(!o)return;let{treeShaking:i}=t;if(!i)return;let{secondarySharedTreeShakingName:s,secondarySharedTreeShakingEntry:l,treeShakingStatus:c}=o;i.status!==c&&(i.status=c,l&&d&&s&&(i.get=async()=>{let e=await (0,n.getRemoteEntry)({origin:a,remoteInfo:{name:s,entry:l,type:d,entryGlobalName:s,shareScope:"default"}});return await e.init(a,r.federation.bundlerRuntime),e.get()}))};return f.forEach(e=>{let[t,r]=e;m(t,r)}),e}}};return(t=a).plugins||(t.plugins=[]),a.plugins.push(u()),s.init(a)}},4298:function(e,t){t.initContainerEntry=function(e){let{webpackRequire:t,shareScope:r,initScope:o,shareScopeKey:n,remoteEntryInitOptions:i}=e;if(!t.S||!t.federation||!t.federation.instance||!t.federation.initOptions)return;let a=t.federation.instance;a.initOptions({name:t.federation.initOptions.name,remotes:[],...i});let s=null==i?void 0:i.shareScopeKeys,l=null==i?void 0:i.shareScopeMap;if(n&&"string"!=typeof n)n.forEach(e=>{if(!s||!l)return void a.initShareScopeMap(e,r,{hostShareScopeMap:(null==i?void 0:i.shareScopeMap)||{}});l[e]||(l[e]={});let t=l[e];a.initShareScopeMap(e,t,{hostShareScopeMap:(null==i?void 0:i.shareScopeMap)||{}})});else{let e=n||"default";Array.isArray(s)?s.forEach(e=>{l[e]||(l[e]={});let t=l[e];a.initShareScopeMap(e,t,{hostShareScopeMap:(null==i?void 0:i.shareScopeMap)||{}})}):a.initShareScopeMap(e,r,{hostShareScopeMap:(null==i?void 0:i.shareScopeMap)||{}})}return(t.federation.attachShareScopeMap&&t.federation.attachShareScopeMap(t),Array.isArray(n))?t.federation.initOptions.shared?t.I(n,o):Promise.all(n.map(e=>t.I(e,o))).then(()=>!0):t.I(n||"default",o)}},6103:function(e,t,r){let o=r(6235),n=r(4260);t.initializeSharing=function(e){let{shareScopeName:t,webpackRequire:r,initPromises:i,initTokens:a,initScope:s}=e,l=Array.isArray(t)?t:[t];var c=[],d=function(e){s||(s=[]);let l=r.federation.instance;var c=a[e];if(c||(c=a[e]={from:l.name}),s.indexOf(c)>=0)return;s.push(c);let d=i[e];if(d)return d;var u=e=>"undefined"!=typeof console&&console.warn&&console.warn(e),p=o=>{var n=e=>u("Initialization of sharing external failed: "+e);try{var i=r(o);if(!i)return;var a=o=>o&&o.init&&o.init(r.S[e],s,{shareScopeMap:r.S||{},shareScopeKeys:t});if(i.then)return f.push(i.then(a,n));var l=a(i);if(l&&"boolean"!=typeof l&&l.then)return f.push(l.catch(n))}catch(e){n(e)}};let f=l.initializeSharing(e,{strategy:l.options.shareStrategy,initScope:s,from:"build"});o.attachShareScopeMap(r);let h=r.federation.bundlerRuntimeOptions.remotes;return(h&&Object.keys(h.idToRemoteMap).forEach(e=>{let t=h.idToRemoteMap[e],r=h.idToExternalAndNameMapping[e][2];if(t.length>1)p(r);else if(1===t.length){let e=t[0];n.FEDERATION_SUPPORTED_TYPES.includes(e.externalType)||p(r)}}),f.length)?i[e]=Promise.all(f).then(()=>i[e]=!0):i[e]=!0};return l.forEach(e=>{c.push(d(e))}),Promise.all(c).then(()=>!0)}},1657:function(e,t,r){let o=r(2910),n=r(5543);function i(e){let{moduleId:t,moduleToHandlerMapping:r,webpackRequire:o,asyncLoad:i}=e,a=o.federation.instance;if(!a)throw Error("Federation instance not found!");let{shareKey:s,shareInfo:l}=r[t];try{let e=n.getUsedExports(o,s),t={...l};if(e&&(t.treeShaking={usedExports:e,useIn:[a.options.name]}),i)return a.loadShare(s,{customShareInfo:t});return a.loadShareSync(s,{customShareInfo:t})}catch(e){throw console.error('loadShareSync failed! The function should not be called unless you set "eager:true". If you do not set it, and encounter this issue, you can check whether an async boundary is implemented.'),console.error("The original error message is as follows: "),e}}t.installInitialConsumes=function(e){o.updateConsumeOptions(e);let{moduleToHandlerMapping:t,webpackRequire:r,installedModules:n,initialConsumes:a,asyncLoad:s}=e,l=[];a.forEach(e=>{let o=()=>i({moduleId:e,moduleToHandlerMapping:t,webpackRequire:r,asyncLoad:s});l.push([e,o])});let c=(e,o)=>{r.m[e]=i=>{var a;n[e]=0,delete r.c[e];let s=o();if("function"!=typeof s)throw Error(`Shared module is not available for eager consumption: ${e}`);let l=s(),{shareInfo:c}=t[e];if((null==c||null==(a=c.shareConfig)?void 0:a.layer)&&l&&"object"==typeof l)try{l.hasOwnProperty("layer")&&void 0!==l.layer||(l.layer=c.shareConfig.layer)}catch(e){}i.exports=l}};if(s)return Promise.all(l.map(async e=>{let[t,r]=e,o=await r();c(t,()=>o)}));l.forEach(e=>{let[t,r]=e;c(t,r)})}},3992:function(e,t,r){r(3473);let o=r(6235),n=r(4260),i=r(2910),a=r(5652);t.remotes=function(e){i.updateRemoteOptions(e);let{chunkId:t,promises:r,webpackRequire:s,chunkMapping:l,idToExternalAndNameMapping:c,idToRemoteMap:d}=e;o.attachShareScopeMap(s),s.o(l,t)&&l[t].forEach(e=>{let t=s.R;t||(t=[]);let o=c[e],i=d[e]||[];if(t.indexOf(o)>=0)return;if(t.push(o),o.p)return r.push(o.p);let l=t=>{t||(t=Error("Container missing")),"string"==typeof t.message&&(t.message+=`
while loading "${o[1]}" from ${o[2]}`),s.m[e]=()=>{throw t},o.p=0},u=(e,t,n,i,a,s)=>{try{let c=e(t,n);if(!c||!c.then)return a(c,i,s);{let e=c.then(e=>a(e,i),l);if(!s)return e;r.push(o.p=e)}}catch(e){l(e)}},p=(e,t,r)=>e?u(s.I,o[0],0,e,f,r):l();var f=(e,r,n)=>u(r.get,o[1],t,0,h,n),h=t=>{o.p=1,s.m[e]=e=>{e.exports=t()}};let m=()=>{try{let e=(0,a.decodeName)(i[0].name,a.ENCODE_NAME_PREFIX)+o[1].slice(1),t=s.federation.instance,r=()=>s.federation.instance.loadRemote(e,{loadFactory:!1,from:"build"});if("version-first"===t.options.shareStrategy){let e=Array.isArray(o[0])?o[0]:[o[0]];return Promise.all(e.map(e=>t.sharedHandler.initializeSharing(e))).then(()=>r())}return r()}catch(e){l(e)}};1===i.length&&n.FEDERATION_SUPPORTED_TYPES.includes(i[0].externalType)&&i[0].name?u(m,o[2],0,0,h,1):u(s,o[2],0,0,p,1)})}},2910:function(e,t){function r(e){var t,r,o,n,i;let{webpackRequire:a,idToExternalAndNameMapping:s={},idToRemoteMap:l={},chunkMapping:c={}}=e,{remotesLoadingData:d}=a,u=null==(o=a.federation)||null==(r=o.bundlerRuntimeOptions)||null==(t=r.remotes)?void 0:t.remoteInfos;if(!d||d._updated||!u)return;let{chunkMapping:p,moduleIdToRemoteDataMapping:f}=d;if(p&&f){for(let[e,t]of Object.entries(f))if(s[e]||(s[e]=[t.shareScope,t.name,t.externalModuleId]),!l[e]&&u[t.remoteName]){let r=u[t.remoteName];(n=l)[i=e]||(n[i]=[]),r.forEach(t=>{l[e].includes(t)||l[e].push(t)})}c&&Object.entries(p).forEach(e=>{let[t,r]=e;c[t]||(c[t]=[]),r.forEach(e=>{c[t].includes(e)||c[t].push(e)})}),d._updated=1}}t.updateConsumeOptions=function(e){let{webpackRequire:t,moduleToHandlerMapping:r}=e,{consumesLoadingData:o,initializeSharingData:n}=t,{sharedFallback:i,bundlerRuntime:a,libraryType:s}=t.federation;if(o&&!o._updated){let{moduleIdToConsumeDataMapping:n={},initialConsumes:l=[],chunkMapping:c={}}=o;if(Object.entries(n).forEach(e=>{let[o,n]=e;r[o]||(r[o]={getter:i?null==a?void 0:a.getSharedFallbackGetter({shareKey:n.shareKey,factory:n.fallback,webpackRequire:t,libraryType:s}):n.fallback,treeShakingGetter:i?n.fallback:void 0,shareInfo:{shareConfig:{requiredVersion:n.requiredVersion,strictVersion:n.strictVersion,singleton:n.singleton,eager:n.eager,layer:n.layer},scope:Array.isArray(n.shareScope)?n.shareScope:[n.shareScope||"default"],treeShaking:i?{get:n.fallback,mode:n.treeShakingMode}:void 0},shareKey:n.shareKey})}),"initialConsumes"in e){let{initialConsumes:t=[]}=e;l.forEach(e=>{t.includes(e)||t.push(e)})}if("chunkMapping"in e){let{chunkMapping:t={}}=e;Object.entries(c).forEach(e=>{let[r,o]=e;t[r]||(t[r]=[]),o.forEach(e=>{t[r].includes(e)||t[r].push(e)})})}o._updated=1}if(n&&!n._updated){let{federation:e}=t;if(!e.instance||!n.scopeToSharingDataMapping)return;let r={};for(let[e,t]of Object.entries(n.scopeToSharingDataMapping))for(let o of t)if("object"==typeof o&&null!==o){let{name:t,version:n,factory:i,eager:a,singleton:s,requiredVersion:l,strictVersion:c}=o,d={requiredVersion:`^${n}`},u=function(e){return void 0!==e};u(s)&&(d.singleton=s),u(l)&&(d.requiredVersion=l),u(a)&&(d.eager=a),u(c)&&(d.strictVersion=c);let p={version:n,scope:[e],shareConfig:d,get:i};r[t]?r[t].push(p):r[t]=[p]}e.instance.registerShared(r),n._updated=1}},t.updateRemoteOptions=r},7549:function(e,t,r){"use strict";var o=r(8203),n=r(6239);r(2977);let i=(e,t)=>{let r=e.findIndex(e=>e.name===t.name);if(r>=0){e[r]=t;return}e.push(t)};class a{add(e){i(this.items,e)}get(e){return this.items.find(t=>t.name===e)}all(){return this.items}constructor(){this.items=[]}}(0,o.Cg)([(0,n._G)()],a)},6847:function(e,t,r){"use strict";r(2977),r(4781),r(5782),r(7549),r(7740),r(5572);var o=r(2855),n=r(5168),i=r.n(n),a=r(6512),s=r(3842),l=r(3297);r(4002),r(6541),r(8811);class c extends l.P{async getVoucherCodes(e,t){let r=this.cfg,o=new URLSearchParams;o.append("cartPriceRule",e.toString()),(null==t?void 0:t.start)!==void 0&&o.append("start",t.start.toString()),(null==t?void 0:t.limit)!==void 0&&o.append("limit",t.limit.toString());let n=`${r.basePath}${r.resourcePath}/get-voucher-codes?${o}`,i=await fetch(n,{method:"GET",headers:{"Content-Type":"application/json"},credentials:"same-origin"});if(!i.ok)throw Error(`Failed to get voucher codes: ${i.statusText}`);return i.json()}async createVoucherCode(e,t){let r=this.cfg,o=`${r.basePath}${r.resourcePath}/create-voucher-code`,n=await fetch(o,{method:"POST",headers:{"Content-Type":"application/json"},credentials:"same-origin",body:JSON.stringify({cartPriceRule:e,code:t})});if(!n.ok)throw Error((await n.json()).message||"Failed to create voucher code");let i=await n.json();if(!i.success)throw Error(i.message||"Failed to create voucher code");return i.data}async generateVoucherCodes(e){let t=this.cfg,r=`${t.basePath}${t.resourcePath}/generate-voucher-codes`,o=await fetch(r,{method:"POST",headers:{"Content-Type":"application/json"},credentials:"same-origin",body:JSON.stringify(e)});if(!o.ok)throw Error((await o.json()).message||"Failed to generate voucher codes");let n=await o.json();if(!n.success)throw Error(n.message||"Failed to generate voucher codes");return n}async deleteVoucherCode(e){let t=this.cfg,r=`${t.basePath}${t.resourcePath}/delete-voucher-code`,o=await fetch(r,{method:"POST",headers:{"Content-Type":"application/json"},credentials:"same-origin",body:JSON.stringify({id:e})});if(!o.ok)throw Error(`Failed to delete voucher code: ${o.statusText}`)}getVoucherCodesExportUrl(e,t){let r=this.cfg,o=new URLSearchParams;return o.append("cartPriceRule",e.toString()),(null==t?void 0:t.start)!==void 0&&o.append("start",t.start.toString()),(null==t?void 0:t.limit)!==void 0&&o.append("limit",t.limit.toString()),`${r.basePath}${r.resourcePath}/export-voucher-codes?${o}`}async getCartItemConfig(){let e=this.cfg,t=`${e.basePath}${e.resourcePath}/get-cart-item-config`,r=await fetch(t,{method:"GET",headers:{"Content-Type":"application/json"},credentials:"same-origin"});if(!r.ok)throw Error(`Failed to get cart item config: ${r.statusText}`);return r.json()}}let d=new c({basePath:"/pimcore-studio/api",resourcePath:"/coreshop/cart_price_rules"}),u=null,p=null,f=async()=>u||p||(p=(async()=>{try{let e=await d.list();return u=(Array.isArray(e)?e:[]).map(e=>({value:e.id,label:e.name??String(e.id)})).filter(e=>null!=e.value&&e.label)}catch(e){return console.error("Failed to load cart price rules:",e),[]}finally{p=null}})());s.DynamicTypeObjectDataAbstractSelect,r(7476),s.DynamicTypePipelineAbstract,r(5848),s.DynamicTypePipelineAbstract,r(2090),s.DynamicTypeGridCellAbstract;var h=r(8203),m=r(6239),g=r(870);class y{async getConfig(){return this.config?this.config:(this.loading||(this.loading=this.api.getConfig().then(e=>(this.config=e,this.loading=null,e))),this.loading)}async isClassAllowedForResource(e,t){let r=await this.getConfig(),o=e.split(".");if(2!==o.length)return!1;let[n,i]=o,a=r.stack[n];if(!a)return!1;let s=a[i];return!!s&&s.includes(t)}async getAllowedClasses(e){let t=await this.getConfig(),r=e.split(".");if(2!==r.length)return[];let[o,n]=r,i=t.stack[o];return i&&i[n]||[]}clearCache(){this.config=null,this.loading=null}constructor(){this.config=null,this.loading=null,this.api=new g.X}}y=(0,h.Cg)([(0,m._G)(),(0,h.Sn)("design:type",Function),(0,h.Sn)("design:paramtypes",[])],y),r(2028);r(1913),r(1976);let b=null;r(3933),r(2696),r(5210),r(3869),r(6222),Symbol.for("coreshop.order.cart_price_rule.condition_registry"),Symbol.for("coreshop.order.cart_price_rule.action_registry"),r(3090);var E=r(9506),S=r(2638);r(9207),r(5180);let{Text:x}=S.A;class _{addListener(e,t,r){let o=arguments.length>3&&void 0!==arguments[3]&&arguments[3],n=arguments.length>4&&void 0!==arguments[4]?arguments[4]:0;this.listeners.has(e)||this.listeners.set(e,[]),this.listeners.get(e).push({callback:t,scope:r,once:o,priority:n})}addListenerOnce(e,t,r){this.addListener(e,t,r,!0,0)}removeListener(e,t){let r=this.listeners.get(e);if(!r)return;let o=r.findIndex(e=>e.callback===t);o>=0&&r.splice(o,1),0===r.length&&this.listeners.delete(e)}fireEvent(e){for(var t=arguments.length,r=Array(t>1?t-1:0),o=1;o<t;o++)r[o-1]=arguments[o];let n=this.listeners.get(e);if(n)for(let t of[...n].sort((e,t)=>e.priority-t.priority))t.callback.apply(t.scope,r),t.once&&this.removeListener(e,t.callback)}hasListeners(e){var t;return((null==(t=this.listeners.get(e))?void 0:t.length)??0)>0}constructor(){this.listeners=new Map}}function v(e){let t=new URLSearchParams;for(let[r,o]of Object.entries(e))"items"===r&&Array.isArray(o)?o.forEach((e,r)=>{t.append(`items[${r}][product]`,String(e.product)),t.append(`items[${r}][quantity]`,String(e.quantity)),void 0!==e.customItemPrice&&t.append(`items[${r}][customItemPrice]`,String(e.customItemPrice)),void 0!==e.customItemDiscount&&t.append(`items[${r}][customItemDiscount]`,String(e.customItemDiscount)),void 0!==e.unitDefinition&&t.append(`items[${r}][unitDefinition]`,String(e.unitDefinition))}):null!=o&&t.append(r,String(o));return t}!function(){window.__CORESHOP_BROKER__||(window.__CORESHOP_BROKER__=new _),window.__CORESHOP_BROKER__}();class R{async getCustomerDetails(e){let t=`${this.basePath}/get-customer-details?customerId=${e}`,r=await fetch(t,{method:"GET",headers:{"Content-Type":"application/json"},credentials:"same-origin"});if(!r.ok)throw Error("Failed to fetch customer details");let o=await r.json();if(!o.success)throw Error("string"==typeof o.message?o.message:"Failed to fetch customer");if(!o.customer)throw Error("Customer not found in response");return o.customer}async preview(e){let t=v(e),r=await fetch(`${this.basePath}/preview`,{method:"POST",headers:{"Content-Type":"application/x-www-form-urlencoded"},credentials:"same-origin",body:t});if(!r.ok)throw Error("Preview request failed");let o=await r.json();if(!o.success)throw Error("string"==typeof o.message?o.message:o.message?JSON.stringify(o.message):"Preview failed");if(!o.data)throw Error("Preview data not found in response");return o.data}async create(e){let t=v(e),r=await fetch(`${this.basePath}/create`,{method:"POST",headers:{"Content-Type":"application/x-www-form-urlencoded"},credentials:"same-origin",body:t});if(!r.ok)throw Error("Create request failed");let o=await r.json();if(!o.success)throw Error("string"==typeof o.message?o.message:o.message?JSON.stringify(o.message):"Create failed");if(void 0===o.id)throw Error("Created ID not found in response");return{success:!0,id:o.id}}constructor(){this.basePath="/pimcore-studio/api/coreshop/order-creation"}}new R;let I=(0,E.rU)(e=>{let{css:t}=e;return{container:t`
    display: flex;
    flex-direction: column;
    height: 100%;
  `,toolbar:t`
    padding: 8px 16px;
    border-bottom: 1px solid #f0f0f0;
    flex-shrink: 0;
  `,listing:t`
    flex: 1;
    overflow: hidden;
  `,loading:t`
    display: flex;
    align-items: center;
    justify-content: center;
    height: 100%;
  `}}),T="coreshop_order",w=(0,E.rU)(e=>{let{css:t}=e;return{container:t`
    display: flex;
    flex-direction: column;
    height: 100%;
  `,toolbar:t`
    padding: 8px 16px;
    border-bottom: 1px solid #f0f0f0;
    flex-shrink: 0;
  `,listing:t`
    flex: 1;
    overflow: hidden;
  `,loading:t`
    display: flex;
    align-items: center;
    justify-content: center;
    height: 100%;
  `}}),M="coreshop_cart",N=(0,E.rU)(e=>{let{css:t}=e;return{container:t`
    display: flex;
    flex-direction: column;
    height: 100%;
  `,toolbar:t`
    padding: 8px 16px;
    border-bottom: 1px solid #f0f0f0;
    flex-shrink: 0;
  `,listing:t`
    flex: 1;
    overflow: hidden;
  `,loading:t`
    display: flex;
    align-items: center;
    justify-content: center;
    height: 100%;
  `}}),$="coreshop_quote";r(1931),r(7755),(0,E.rU)(e=>{let{css:t,token:r}=e;return{container:t`
    container-type: inline-size;
    display: flex;
    flex-direction: column;
    height: 100%;
    overflow: auto;
    padding: 8px 12px;
    background: ${r.colorBgLayout};
    gap: 16px;
  `,toolbar:t`
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 8px;
    background: ${r.colorBgContainer};
    border-radius: ${r.borderRadiusLG}px;
    border: 1px solid ${r.colorBorderSecondary};
  `,statesRow:t`
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 10px;

    @container (min-width: 600px) {
      grid-template-columns: repeat(4, 1fr);
    }
  `,stateCard:t`
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 10px;
    padding: 14px 12px;
    background: ${r.colorBgContainer};
    border-radius: ${r.borderRadiusLG}px;
    border: 1px solid ${r.colorBorderSecondary};
  `,metricCard:t`
    display: flex;
    flex-direction: column;
    gap: 8px;
    padding: 16px;
    background: ${r.colorBgContainer};
    border-radius: ${r.borderRadiusLG}px;
    border: 1px solid ${r.colorBorderSecondary};
  `,columnsArea:t`
    display: flex;
    flex-direction: column;
    gap: 16px;

    @container (min-width: 800px) {
      flex-direction: row;
    }
  `,column:t`
    min-width: 0;
    display: flex;
    flex-direction: column;
    gap: 16px;

    @container (min-width: 800px) {
      flex: 1 1 50%;
    }
  `,card:t`
    border-radius: ${r.borderRadiusLG}px;
    border: 1px solid ${r.colorBorderSecondary};
    box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.03);
  `,customerSkeleton:t`
    display: flex;
    align-items: center;
    gap: 14px;
  `}});let k=["coreshop-order-detail","coreshop-cart-detail","coreshop-quote-detail"];class A{supports(e){var t;return void 0!==e.component&&k.includes(e.component)&&"number"==typeof(null==(t=e.config)?void 0:t.orderId)}cleanConfig(e){var t;return{...e,config:{orderId:null==(t=e.config)?void 0:t.orderId}}}restore(e,t){var r;return"number"==typeof(null==(r=e.config)?void 0:r.orderId)&&!!(e.config.orderId>0)}}new A;class O{register(e,t){this.tabs.set(e,t)}get(e){return this.tabs.get(e)}has(e){return this.tabs.has(e)}getAll(){return Array.from(this.tabs.values())}getForType(e){return Array.from(this.tabs.values()).filter(t=>t.types.includes(e)).sort((e,t)=>e.priority-t.priority)}unregister(e){this.tabs.delete(e)}clear(){this.tabs.clear()}constructor(){this.tabs=new Map}}O=(0,h.Cg)([(0,m._G)()],O),r(7119),r(4658),(0,E.rU)(e=>{let{css:t,token:r}=e;return{container:t`
    display: flex;
    flex-direction: column;
    gap: 12px;
  `,statesRow:t`
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 10px;

    @container (min-width: 600px) {
      grid-template-columns: repeat(4, 1fr);
    }
  `,stateCard:t`
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 8px;
    padding: 14px 12px;
    background: ${r.colorBgContainer};
    border-radius: ${r.borderRadiusLG}px;
    border: 1px solid ${r.colorBorderSecondary};
  `,stateLabel:t`
    font-size: 11px;
    font-weight: 500;
    color: ${r.colorTextTertiary};
    text-transform: uppercase;
    letter-spacing: 0.05em;
  `,statePill:t`
    display: inline-flex;
    align-items: center;
    padding: 3px 12px;
    border-radius: 999px;
    font-size: 12px;
    font-weight: 600;
    color: #fff;
    line-height: 1.4;
    white-space: nowrap;
  `,metricsRow:t`
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 10px;

    @container (min-width: 600px) {
      grid-template-columns: repeat(4, 1fr);
    }
  `,metricCard:t`
    display: flex;
    flex-direction: column;
    gap: 4px;
    padding: 16px;
    background: ${r.colorBgContainer};
    border-radius: ${r.borderRadiusLG}px;
    border: 1px solid ${r.colorBorderSecondary};
  `,metricCardHighlight:t`
    display: flex;
    flex-direction: column;
    gap: 4px;
    padding: 16px;
    background: ${r.colorBgContainer};
    border-radius: ${r.borderRadiusLG}px;
    border: 1px solid ${r.colorPrimary}40;
    box-shadow: 0 0 0 1px ${r.colorPrimary}10;
  `,metricLabel:t`
    font-size: 11px;
    font-weight: 500;
    color: ${r.colorTextTertiary};
    text-transform: uppercase;
    letter-spacing: 0.05em;
  `,metricValue:t`
    font-size: 16px;
    font-weight: 600;
    color: ${r.colorText};
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
  `,metricValueLarge:t`
    font-size: 20px;
    font-weight: 700;
    color: ${r.colorText};
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
  `}}),(0,E.rU)(e=>{let{css:t,token:r}=e;return{card:t``,container:t`
    display: flex;
    flex-direction: column;
  `,customerHeader:t`
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 0 0 8px 0;
  `,avatar:t`
    width: 44px;
    height: 44px;
    border-radius: 50%;
    background: ${r.colorPrimary}15;
    color: ${r.colorPrimary};
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
    font-weight: 600;
    flex-shrink: 0;
  `,customerInfo:t`
    display: flex;
    flex-direction: column;
    gap: 2px;
    min-width: 0;
  `,customerName:t`
    font-size: 14px;
    font-weight: 600;
    color: ${r.colorText};
  `,customerEmail:t`
    font-size: 13px;
    color: ${r.colorTextSecondary};
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
  `,customerSince:t`
    font-size: 11px;
    color: ${r.colorTextTertiary};
  `,tabs:t`
    .ant-tabs-nav {
      margin-bottom: 0;
      padding: 0 16px;
    }

    .ant-tabs-content {
      padding: 0;
    }
  `,addressContent:t`
    display: flex;
    align-items: flex-start;
    gap: 10px;
    padding: 14px 16px;
  `,addressIcon:t`
    color: ${r.colorTextTertiary};
    font-size: 16px;
    margin-top: 2px;
    flex-shrink: 0;
  `,addressText:t`
    flex: 1;
    white-space: pre-line;
    line-height: 1;
    color: ${r.colorText};
  `,noData:t`
    padding: 16px;
    color: ${r.colorTextTertiary};
    font-size: 13px;
  `}}),(0,E.rU)(e=>{let{css:t,token:r}=e;return{card:t`
    .ant-card-body {
      padding: 0;
    }

    .ant-empty {
      padding: 24px 16px;
      margin: 0;
      margin-block: 0;
    }
  `,table:t`
    .ant-table-thead > tr > th {
      background: ${r.colorBgLayout};
      font-weight: 600;
      font-size: 12px;
      color: ${r.colorTextSecondary};
      padding: 8px 16px !important;
      white-space: nowrap;
    }

    .ant-table-thead > tr > th:first-child {
      padding-left: 24px !important;
    }

    .ant-table-thead > tr > th:last-child {
      padding-right: 24px !important;
    }

    .ant-table-tbody > tr > td {
      padding: 10px 16px !important;
    }

    .ant-table-tbody > tr > td:first-child {
      padding-left: 24px !important;
    }

    .ant-table-tbody > tr > td:last-child {
      padding-right: 24px !important;
    }

    .ant-table-tbody > tr:hover > td {
      background: ${r.colorPrimaryBg} !important;
    }
  `,dimText:t`
    color: ${r.colorTextSecondary};
    font-size: 12px;
  `,statusBadge:t`
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 2px 10px;
    border-radius: 999px;
    font-size: 11px;
    font-weight: 600;
    color: #fff;
    white-space: nowrap;
    line-height: 1.5;
  `,statusBadgeClickable:t`
    cursor: pointer;
    transition: opacity 0.15s;

    &:hover {
      opacity: 0.85;
    }
  `}}),(0,E.rU)(e=>{let{css:t,token:r}=e;return{container:t``,monoNum:t`
    font-variant-numeric: tabular-nums;
  `,priceRulesSection:t`
    padding: 12px 16px;
    border-top: 1px solid ${r.colorBorderSecondary};
    background: ${r.colorBgLayout};
  `,sectionLabel:t`
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: ${r.colorTextTertiary};
    margin-bottom: 8px;
  `,priceRulesTable:t`
    .ant-table-tbody > tr > td {
      border-bottom: none;
      padding: 6px 0;
      background: transparent;
    }

    .ant-table {
      background: transparent;
    }
  `,discountValue:t`
    font-weight: 600;
    color: ${r.colorError};
    font-variant-numeric: tabular-nums;
  `,summarySection:t`
    padding: 20px 24px;
    background: ${r.colorBgLayout};
    border-top: 1px solid ${r.colorBorderSecondary};
    display: flex;
    justify-content: flex-end;
  `,summaryWrapper:t`
    min-width: 300px;
    max-width: 400px;
    width: 100%;
  `,summaryRow:t`
    display: flex;
    align-items: baseline;
    padding: 6px 0;
    border-bottom: 1px dashed ${r.colorBorderSecondary};

    &:last-child {
      border-bottom: none;
    }
  `,summaryRowTotal:t`
    border-bottom: none;
    border-top: 2px solid ${r.colorBorder};
    padding-top: 10px;
    margin-top: 4px;
  `,summaryLabel:t`
    flex: 1;
    text-align: right;
    padding-right: 20px;
    font-size: 13px;
    color: ${r.colorTextSecondary};
  `,summaryValue:t`
    text-align: right;
    font-size: 13px;
    font-weight: 500;
    color: ${r.colorText};
    min-width: 100px;
  `}}),(0,E.rU)(e=>{let{css:t,token:r}=e;return{content:t`
    padding: 16px 0;
  `,description:t`
    margin-bottom: 24px;
    color: ${r.colorText};
    font-size: 14px;
  `,buttonContainer:t`
    display: flex;
    gap: 8px;
  `,transitionButton:t`
    &:hover:not(:disabled) {
      opacity: 0.9 !important;
      background-color: #524646 !important;
    }

    &:disabled {
      opacity: 0.5 !important;
      background-color: #524646 !important;
    }

    &:focus {
      background-color: #524646 !important;
    }
  `}}),(0,E.rU)(e=>{let{css:t,token:r}=e;return{content:t`
    padding: 16px 0;
  `,field:t`
    display: flex;
    padding: 12px 0;
    border-bottom: 1px solid ${r.colorBorder};
  `,label:t`
    width: 180px;
    font-weight: 500;
    color: ${r.colorTextSecondary};
    flex-shrink: 0;
  `,value:t`
    flex: 1;
    color: ${r.colorText};
  `,buttonContainer:t`
    margin: 16px 0;
    padding: 4px 0 16px 0;
    border-bottom: 1px solid ${r.colorBorder};
  `,productsSection:t`
    margin-top: 24px;
  `,productsHeader:t`
    font-size: 16px;
    font-weight: 600;
    margin-bottom: 12px;
    color: ${r.colorText};
  `}}),(0,E.rU)(e=>{let{css:t,token:r}=e;return{content:t`
    padding: 16px 0;
  `,field:t`
    display: flex;
    padding: 12px 0;
    border-bottom: 1px solid ${r.colorBorder};
  `,label:t`
    width: 180px;
    font-weight: 500;
    color: ${r.colorTextSecondary};
    flex-shrink: 0;
  `,value:t`
    flex: 1;
    color: ${r.colorText};
  `,buttonContainer:t`
    margin: 16px 0;
    padding: 4px 0 16px 0;
    border-bottom: 1px solid ${r.colorBorder};
  `,detailsSection:t`
    margin-top: 24px;
  `,detailsHeader:t`
    font-size: 16px;
    font-weight: 600;
    margin-bottom: 12px;
    color: ${r.colorText};
  `}}),r(4478),(0,E.rU)(e=>{let{css:t,token:r}=e;return{detailContent:t`
    padding: 8px 0;
  `,detailRow:t`
    display: flex;
    padding: 10px 0;
    border-bottom: 1px solid ${r.colorBorderSecondary};

    &:last-of-type {
      border-bottom: none;
    }
  `,detailLabel:t`
    width: 180px;
    font-size: 13px;
    font-weight: 500;
    color: ${r.colorTextSecondary};
    flex-shrink: 0;
  `,detailValue:t`
    flex: 1;
    font-size: 13px;
    color: ${r.colorText};
  `,detailsHeader:t`
    font-size: 13px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    margin: 20px 0 10px 0;
    color: ${r.colorTextSecondary};
  `,detailRowBody:t`
    padding: 12px;
    background: ${r.colorBgLayout};
    white-space: normal;
    word-wrap: break-word;
    border-radius: ${r.borderRadius}px;
  `}}),(0,E.rU)(e=>{let{css:t,token:r}=e;return{trackingCode:t`
    font-family: 'SF Mono', 'Menlo', 'Monaco', monospace;
    font-size: 12px;
    background: ${r.colorBgLayout};
    padding: 2px 6px;
    border-radius: ${r.borderRadiusSM}px;
    color: ${r.colorText};
  `}});let{TextArea:P}=r(3964).A;(0,E.rU)(e=>{let{css:t,token:r}=e;return{card:t`
    .ant-card-body {
      padding: 0;
    }
  `,emptyState:t`
    padding: 24px 16px;
    text-align: center;

    .ant-empty {
      margin-block: 0;
    }
  `,commentsList:t`
    display: flex;
    flex-direction: column;
    max-height: 400px;
    overflow-y: auto;
  `,commentItem:t`
    display: flex;
    gap: 12px;
    padding: 14px 16px;
    border-bottom: 1px solid ${r.colorBorderSecondary};
    transition: background 0.15s;

    &:last-child {
      border-bottom: none;
    }

    &:hover {
      background: ${r.colorBgLayout};

      .ant-btn-dangerous {
        opacity: 1;
      }
    }
  `,commentAvatar:t`
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: ${r.colorPrimary}12;
    color: ${r.colorPrimary};
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 11px;
    font-weight: 600;
    flex-shrink: 0;
    margin-top: 2px;
  `,commentBody:t`
    flex: 1;
    min-width: 0;
  `,commentHeader:t`
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 4px;
  `,commentAuthor:t`
    font-size: 13px;
    font-weight: 600;
    color: ${r.colorText};
  `,commentDate:t`
    font-size: 11px;
    color: ${r.colorTextTertiary};
    flex: 1;
  `,deleteButton:t`
    opacity: 0;
    transition: opacity 0.15s;
  `,commentText:t`
    font-size: 13px;
    color: ${r.colorText};
    white-space: pre-wrap;
    word-wrap: break-word;
    line-height: 1.5;
  `,commentBadge:t`
    display: inline-flex;
    align-items: center;
    font-size: 11px;
    color: ${r.colorPrimary};
    margin-top: 6px;
    padding: 2px 8px;
    background: ${r.colorPrimary}08;
    border-radius: ${r.borderRadiusSM}px;
  `}}),(0,E.rU)(e=>{let{css:t,token:r}=e;return{card:t``,transitionButton:t`
    font-weight: 500;
    border-width: 1.5px;

    &:hover {
      opacity: 0.85;
    }
  `,cancelButton:t`
    color: ${r.colorError} !important;
    border-color: ${r.colorError} !important;
    font-weight: 500;

    &:hover {
      color: #fff !important;
      background-color: ${r.colorError} !important;
      border-color: ${r.colorError} !important;
    }
  `,emptyTimeline:t`
    padding: 24px;
    text-align: center;
    color: ${r.colorTextTertiary};
    font-size: 13px;
  `,timelineContainer:t`
    max-height: 280px;
    overflow-y: auto;
    padding-right: 4px;
  `,timelineItem:t`
    position: relative;
    padding-left: 24px;
    padding-bottom: 16px;

    &:last-child {
      padding-bottom: 0;
    }
  `,timelineDot:t`
    position: absolute;
    left: 0;
    top: 6px;
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: ${r.colorPrimary};
    z-index: 1;
  `,timelineLine:t`
    position: absolute;
    left: 3px;
    top: 18px;
    bottom: 0;
    width: 2px;
    background: ${r.colorBorderSecondary};
  `,timelineContent:t`
    display: flex;
    flex-direction: column;
    gap: 2px;
  `,timelineHeader:t`
    display: flex;
    align-items: baseline;
    gap: 8px;
  `,timelineTitle:t`
    font-size: 13px;
    font-weight: 500;
    color: ${r.colorText};
  `,timelineDescription:t`
    font-size: 12px;
    color: ${r.colorTextTertiary};
  `,timelineDate:t`
    font-size: 11px;
    color: ${r.colorTextTertiary};
    white-space: nowrap;
    margin-left: auto;
  `}}),(0,E.rU)(e=>{let{css:t,token:r}=e;return{modal:t`
    .ant-modal-body {
      padding: 0;
    }
  `,iframeContainer:t`
    width: 100%;
    height: 500px;
    overflow: hidden;
    border-radius: 0 0 ${r.borderRadiusLG}px ${r.borderRadiusLG}px;
  `,iframe:t`
    width: 100%;
    height: 100%;
    border: none;
  `}});class C{register(e,t){this.steps.set(e,t)}get(e){return this.steps.get(e)}has(e){return this.steps.has(e)}getAll(){return Array.from(this.steps.values())}getSorted(){return this.getAll().sort((e,t)=>e.priority-t.priority)}unregister(e){this.steps.delete(e)}clear(){this.steps.clear()}constructor(){this.steps=new Map}}C=(0,h.Cg)([(0,m._G)()],C),Symbol.for("CoreShop/Order/OrderCreation/StepRegistry");let L=null;(0,E.rU)(e=>{let{css:t}=e;return{selectorCard:t`
    max-width: 600px;
    margin: 0 auto;
  `,selectButton:t`
    min-width: 200px;
  `}}),(0,E.rU)(e=>{let{css:t,token:r}=e;return{customerRow:t`
    display: flex;
    align-items: center;
    gap: 12px;
  `,avatar:t`
    flex-shrink: 0;
    background: ${r.colorPrimary};
    font-weight: 600;
  `,info:t`
    flex: 1;
    min-width: 0;
  `,name:t`
    font-weight: 600;
    font-size: 14px;
    line-height: 1.3;
  `,email:t`
    font-size: 12px;
    color: ${r.colorTextSecondary};
    line-height: 1.3;
  `}});let F=(0,E.rU)(e=>{let{css:t,token:r}=e;return{container:t`
    container-type: inline-size;
    display: flex;
    flex-direction: column;
    height: 100%;
    overflow: auto;
    padding: 16px;
    background: ${r.colorBgLayout};
    gap: 16px;
  `,toolbar:t`
    display: flex;
    align-items: center;
    gap: 8px;
    background: ${r.colorBgContainer};
    border: 1px solid ${r.colorBorderSecondary};
    border-radius: ${r.borderRadiusLG}px;
    box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.03);
    padding: 8px;
    position: sticky;
    top: 0;
    z-index: 10;
  `,toolbarSpacer:t`
    flex: 1;
  `,content:t`
    display: flex;
    flex-direction: column;
    gap: 16px;
  `,topRow:t`
    display: flex;
    flex-direction: column;
    gap: 16px;

    @container (min-width: 700px) {
      flex-direction: row;
      align-items: stretch;

      > :first-child {
        flex: 0 0 auto;
        min-width: 280px;
        max-width: 340px;
      }

      > :last-child {
        flex: 1;
        min-width: 0;
      }

      > * > .ant-card {
        height: 100%;
      }
    }
  `,multiRow:t`
    display: flex;
    flex-direction: column;
    gap: 16px;

    @container (min-width: 800px) {
      flex-direction: row;

      > * {
        flex: 1;
        min-width: 0;
      }

      > * > .ant-card {
        height: 100%;
      }
    }
  `,block:t`
    .ant-card {
      border-radius: ${r.borderRadiusLG}px;
      border: 1px solid ${r.colorBorderSecondary};
      box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.03), 0 1px 6px -1px rgba(0, 0, 0, 0.02);
      overflow: hidden;

      .ant-card-head {
        border-bottom: 1px solid ${r.colorBorderSecondary};
        min-height: 44px;
        padding: 0 16px;
        font-size: 13px;
        font-weight: 600;
        letter-spacing: 0.01em;
        text-transform: uppercase;
        color: ${r.colorTextSecondary};
      }

      .ant-card-head-title {
        font-size: 13px;
        padding: 10px 0;
      }

      .ant-card-extra {
        padding: 6px 0;
      }

      .ant-card-body {
        > div:last-child {
          margin-bottom: 0;
        }

        .ant-form-item:last-child {
          margin-bottom: 0;
        }

        .ant-row:last-child .ant-form-item {
          margin-bottom: 0;
        }
      }
    }
  `,blockDisabled:t`
    position: relative;
    pointer-events: none;

    &::after {
      content: '';
      position: absolute;
      inset: 0;
      background: ${r.colorBgContainer};
      opacity: 0.55;
      z-index: 1;
      border-radius: ${r.borderRadiusLG}px;
    }
  `}}),D=async()=>{try{let e=container.get(coreshopResourceServiceIds.configProvider),t=await e.getAllowedClasses("coreshop.customer");return t.length>0?t:["CoreShopCustomer"]}catch{return["CoreShopCustomer"]}};class U{supports(e){var t;return"coreshop-order-creation-detail"===e.component&&"number"==typeof(null==(t=e.config)?void 0:t.customerId)}cleanConfig(e){var t;return{...e,config:{customerId:null==(t=e.config)?void 0:t.customerId}}}restore(e,t){var r;return"number"==typeof(null==(r=e.config)?void 0:r.customerId)&&e.config.customerId>0}}new U,(0,E.rU)(e=>{let{css:t,token:r}=e;return{emptyState:t`
    padding: 32px;
    text-align: center;
    background: ${r.colorBgLayout};
    border-radius: ${r.borderRadius}px;
  `,addButton:t`
    margin-top: 16px;
  `}}),(0,E.rU)(e=>{let{css:t,token:r}=e;return{summaryWrapper:t`
    display: flex;
    flex-direction: column;
    gap: 0;
  `,summaryRow:t`
    display: flex;
    align-items: baseline;
    justify-content: space-between;
    padding: 6px 0;
    border-bottom: 1px solid ${r.colorBorderSecondary};
  `,summaryRowTotal:t`
    border-bottom: none;
    border-top: 2px solid ${r.colorBorder};
    padding-top: 10px;
    margin-top: 4px;
  `,summaryLabel:t`
    font-size: 13px;
    color: ${r.colorTextSecondary};
  `,summaryValues:t`
    display: flex;
    gap: 16px;
  `,summaryValue:t`
    text-align: right;
    font-size: 13px;
    font-weight: 500;
    color: ${r.colorText};
    min-width: 90px;
    font-variant-numeric: tabular-nums;
  `}})},1931:function(e,t,r){"use strict";r(2855),r(5168);var o=r(9506);r(2977),r(7119),r(4658),r(4478),(0,o.rU)(e=>{let{css:t,token:r}=e;return{container:t`
    container-type: inline-size;
    display: flex;
    flex-direction: column;
    height: 100%;
    overflow: auto;
    padding: 8px 12px;
    background: ${r.colorBgLayout};
    gap: 16px;
  `,topArea:t`
    margin-bottom: 0;
  `,columnsArea:t`
    display: flex;
    flex-direction: column;
    gap: 16px;

    @container (min-width: 800px) {
      flex-direction: row;
    }
  `,leftColumn:t`
    min-width: 0;
    display: flex;
    flex-direction: column;
    gap: 16px;

    @container (min-width: 800px) {
      flex: 1 1 50%;
    }
  `,rightColumn:t`
    min-width: 0;
    display: flex;
    flex-direction: column;
    gap: 16px;

    @container (min-width: 800px) {
      flex: 1 1 50%;
    }
  `,bottomArea:t`
    margin-bottom: 0;
  `,block:t`
    .ant-card {
      border-radius: ${r.borderRadiusLG}px;
      border: 1px solid ${r.colorBorderSecondary};
      box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.03), 0 1px 6px -1px rgba(0, 0, 0, 0.02);
      overflow: hidden;

      .ant-card-head {
        border-bottom: 1px solid ${r.colorBorderSecondary};
        min-height: 44px;
        padding: 0 16px;
        font-size: 13px;
        font-weight: 600;
        letter-spacing: 0.01em;
        text-transform: uppercase;
        color: ${r.colorTextSecondary};
      }

      .ant-card-head-title {
        font-size: 13px;
        padding: 10px 0;
      }

      .ant-card-extra {
        padding: 6px 0;
      }
    }
  `,emptyState:t`
    display: flex;
    align-items: center;
    justify-content: center;
    height: 100%;
    min-height: 400px;
  `,emptyStateText:t`
    color: ${r.colorTextTertiary};
    font-size: 14px;
  `}})},4478:function(e,t,r){"use strict";r(2855),r(5168);var o=r(9506);r(4658),(0,o.rU)(e=>{let{css:t,token:r}=e;return{toolbar:t`
    display: flex;
    align-items: center;
    gap: 8px;
    background: ${r.colorBgContainer};
    border: 1px solid ${r.colorBorderSecondary};
    border-radius: ${r.borderRadiusLG}px;
    box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.03);
    padding: 8px;
    position: sticky;
    top: 0;
    z-index: 10;
  `,spacer:t`
    flex: 1;
  `}})},4658:function(e,t,r){"use strict";r(2855),r(5168)},7119:function(){"use strict";Symbol.for("coreshop.order.sale_tab_registry"),Symbol.for("coreshop.order.order_api"),Symbol.for("coreshop.order.cart_api"),Symbol.for("coreshop.order.quote_api")},9207:function(){"use strict"},1913:function(e,t,r){"use strict";r(4781)},6808:function(e,t,r){"use strict";r(2855),r(5168),r(2977)},6541:function(e,t,r){"use strict";r(2605),r(2855),r(5168),r(2696),r(5210),r(6808),r(2977),r(6222),r(3376),r(234),r(4296)},4002:function(e,t,r){"use strict";r(6541);let o=e=>{let t=null==e?void 0:e.displayName;return"string"==typeof t&&t.startsWith("SchemaCondition(")},n=e=>{let t=null==e?void 0:e.displayName;return"string"==typeof t&&t.startsWith("SchemaAction(")},i=(e,t)=>{var r;if(!e||!t)return 0;let o=null==(r=e[t])?void 0:r.fields;return Array.isArray(o)?o.length:0},a=(e,t,r,o)=>{if(!o)return;let n=Object.entries(o).filter(r=>{let[o,n]=r;if(!o.includes(`_${e}_`)||!o.endsWith(`_${t}`))return!1;let i=null==n?void 0:n.fields;return Array.isArray(i)&&i.length>0}).map(e=>{let[t]=e;return t});if(0!==n.length){if(r){let t=`_${e}_`,o=r.indexOf(t);if(-1!==o){let e=r.slice(0,o+t.length),i=n.find(t=>t.startsWith(e));if(i)return i}}return n[0]}},s=(e,t,r,o)=>{if(i(o,r)>0)return r;let n=a(e,t,r,o);return n||r}},3869:function(e,t,r){"use strict";r.d(t,{$J:()=>i.$,Dq:()=>n.D,Sx:()=>s.Sx,XH:()=>s.XH,Y8:()=>o.Y,ir:()=>s.ir,q:()=>a.q});var o=r(9986),n=r(4929),i=r(9035),a=r(4296),s=r(7281)},7559:function(e,t,r){"use strict";var o,n,i,a,s,l,c,d,u,p,f,h,m=r(281),g=r.n(m);let y=[],b={"@pimcore/studio-ui-bundle":[{alias:"@pimcore/studio-ui-bundle",externalType:"promise",shareScope:"default"}]},E="coreshoporder",S="version-first";if((r.initializeSharingData||r.initializeExposesData)&&r.federation){let e=(e,t,r)=>{e&&e[t]&&(e[t]=r)},t=(e,t,r)=>{var o,n,i,a,s,l;let c=r();Array.isArray(c)?(null!=(i=(o=e)[n=t])||(o[n]=[]),e[t].push(...c)):"object"==typeof c&&null!==c&&(null!=(l=(a=e)[s=t])||(a[s]={}),Object.assign(e[t],c))},m=(e,t,r)=>{var o,n,i;null!=(i=(o=e)[n=t])||(o[n]=r())},x=null!=(d=null==(o=r.remotesLoadingData)?void 0:o.chunkMapping)?d:{},_=null!=(u=null==(n=r.remotesLoadingData)?void 0:n.moduleIdToRemoteDataMapping)?u:{},v=null!=(p=null==(i=r.initializeSharingData)?void 0:i.scopeToSharingDataMapping)?p:{},R=null!=(f=null==(a=r.consumesLoadingData)?void 0:a.chunkMapping)?f:{},I=null!=(h=null==(s=r.consumesLoadingData)?void 0:s.moduleIdToConsumeDataMapping)?h:{},T={},w=[],M={},N=null==(l=r.initializeExposesData)?void 0:l.shareScope;for(let e in g())r.federation[e]=g()[e];m(r.federation,"consumesLoadingModuleToHandlerMapping",()=>{let e={};for(let[t,r]of Object.entries(I))e[t]={getter:r.fallback,shareInfo:{shareConfig:{fixedDependencies:!1,requiredVersion:r.requiredVersion,strictVersion:r.strictVersion,singleton:r.singleton,eager:r.eager},scope:[r.shareScope]},shareKey:r.shareKey};return e}),m(r.federation,"initOptions",()=>({})),m(r.federation.initOptions,"name",()=>E),m(r.federation.initOptions,"shareStrategy",()=>S),m(r.federation.initOptions,"shared",()=>{let e={};for(let[t,r]of Object.entries(v))for(let o of r)if("object"==typeof o&&null!==o){let{name:r,version:n,factory:i,eager:a,singleton:s,requiredVersion:l,strictVersion:c}=o,d={},u=function(e){return void 0!==e};u(s)&&(d.singleton=s),u(l)&&(d.requiredVersion=l),u(a)&&(d.eager=a),u(c)&&(d.strictVersion=c);let p={version:n,scope:[t],shareConfig:d,get:i};e[r]?e[r].push(p):e[r]=[p]}return e}),t(r.federation.initOptions,"remotes",()=>Object.values(b).flat().filter(e=>"script"===e.externalType)),t(r.federation.initOptions,"plugins",()=>y),m(r.federation,"bundlerRuntimeOptions",()=>({})),m(r.federation.bundlerRuntimeOptions,"remotes",()=>({})),m(r.federation.bundlerRuntimeOptions.remotes,"chunkMapping",()=>x),m(r.federation.bundlerRuntimeOptions.remotes,"remoteInfos",()=>b),m(r.federation.bundlerRuntimeOptions.remotes,"idToExternalAndNameMapping",()=>{let e={};for(let[t,r]of Object.entries(_))e[t]=[r.shareScope,r.name,r.externalModuleId,r.remoteName];return e}),m(r.federation.bundlerRuntimeOptions.remotes,"webpackRequire",()=>r),t(r.federation.bundlerRuntimeOptions.remotes,"idToRemoteMap",()=>{let e={};for(let[t,r]of Object.entries(_)){let o=b[r.remoteName];o&&(e[t]=o)}return e}),e(r,"S",r.federation.bundlerRuntime.S),r.federation.attachShareScopeMap&&r.federation.attachShareScopeMap(r),e(r.f,"remotes",(e,t)=>r.federation.bundlerRuntime.remotes({chunkId:e,promises:t,chunkMapping:x,idToExternalAndNameMapping:r.federation.bundlerRuntimeOptions.remotes.idToExternalAndNameMapping,idToRemoteMap:r.federation.bundlerRuntimeOptions.remotes.idToRemoteMap,webpackRequire:r})),e(r.f,"consumes",(e,t)=>r.federation.bundlerRuntime.consumes({chunkId:e,promises:t,chunkMapping:R,moduleToHandlerMapping:r.federation.consumesLoadingModuleToHandlerMapping,installedModules:T,webpackRequire:r})),e(r,"I",(e,t)=>r.federation.bundlerRuntime.I({shareScopeName:e,initScope:t,initPromises:w,initTokens:M,webpackRequire:r})),e(r,"initContainer",(e,t,o)=>r.federation.bundlerRuntime.initContainerEntry({shareScope:e,initScope:t,remoteEntryInitOptions:o,shareScopeKey:N,webpackRequire:r})),e(r,"getContainer",(e,t)=>{var o=r.initializeExposesData.moduleMap;return r.R=t,t=Object.prototype.hasOwnProperty.call(o,e)?o[e]():Promise.resolve().then(()=>{throw Error('Module "'+e+'" does not exist in container.')}),r.R=void 0,t}),r.federation.instance=r.federation.runtime.init(r.federation.initOptions),(null==(c=r.consumesLoadingData)?void 0:c.initialConsumes)&&r.federation.bundlerRuntime.installInitialConsumes({webpackRequire:r,installedModules:T,initialConsumes:r.consumesLoadingData.initialConsumes,moduleToHandlerMapping:r.federation.consumesLoadingModuleToHandlerMapping})}},7042:function(e){"use strict";e.exports=new Promise(e=>{let t=window.StudioUIBundleRemoteUrl,r=document.createElement("script"),o=!1;(document.querySelectorAll("script").forEach(e=>{if(e.src.replace(/https?:\/\/[^/]+/,"")===t.replace(/https?:\/\/[^/]+/,"")){o=!0;return}}),o)?e({get:e=>window.pimcore_studio_ui_bundle.get(e),init:(...e)=>{try{return window.pimcore_studio_ui_bundle.init(...e)}catch(e){console.log("remote container already initialized")}}}):(r.src=t,r.onload=()=>{e({get:e=>window.pimcore_studio_ui_bundle.get(e),init:(...e)=>{try{return window.pimcore_studio_ui_bundle.init(...e)}catch(e){console.log("remote container already initialized")}}})},document.head.appendChild(r))})}},__webpack_module_cache__={};function __webpack_require__(e){var t=__webpack_module_cache__[e];if(void 0!==t)return t.exports;var r=__webpack_module_cache__[e]={exports:{}};return __webpack_modules__[e](r,r.exports,__webpack_require__),r.exports}__webpack_require__.m=__webpack_modules__,__webpack_require__.c=__webpack_module_cache__,__webpack_require__.x=()=>{var e=__webpack_require__.O(void 0,["476","387","418","466","468","419","124","843","57","110","459","398","200"],()=>__webpack_require__(6847));return __webpack_require__.O(e)},(()=>{__webpack_require__.federation||(__webpack_require__.federation={chunkMatcher:function(e){return!/^(110|57)$/.test(e)},rootOutputDir:"../../"})})(),(()=>{__webpack_require__.n=e=>{var t=e&&e.__esModule?()=>e.default:()=>e;return __webpack_require__.d(t,{a:t}),t}})(),(()=>{__webpack_require__.d=(e,t)=>{for(var r in t)__webpack_require__.o(t,r)&&!__webpack_require__.o(e,r)&&Object.defineProperty(e,r,{enumerable:!0,get:t[r]})}})(),(()=>{__webpack_require__.f={},__webpack_require__.e=e=>Promise.all(Object.keys(__webpack_require__.f).reduce((t,r)=>(__webpack_require__.f[r](e,t),t),[]))})(),(()=>{__webpack_require__.u=e=>"static/js/async/"+e+"."+({232:"8caceb6a",375:"95a8698f",44:"e2626666",460:"beee66a9",507:"53f01076",582:"d2203da4",63:"b5c86d7e",663:"32dbbfc8",695:"2d82c157",70:"c41ffa80",76:"2f7f2bb2",760:"7aa857e8",79:"f70a5d07",851:"9605fce5",920:"ff964e5f"})[e]+".js"})(),(()=>{__webpack_require__.miniCssF=e=>""+e+".css"})(),(()=>{__webpack_require__.g=(()=>{if("object"==typeof globalThis)return globalThis;try{return this||Function("return this")()}catch(e){if("object"==typeof window)return window}})()})(),(()=>{__webpack_require__.o=(e,t)=>Object.prototype.hasOwnProperty.call(e,t)})(),(()=>{var e={},t="coreshoporder:";__webpack_require__.l=function(r,o,n,i){if(e[r])return void e[r].push(o);if(void 0!==n)for(var a,s,l=document.getElementsByTagName("script"),c=0;c<l.length;c++){var d=l[c];if(d.getAttribute("src")==r||d.getAttribute("data-webpack")==t+n){a=d;break}}a||(s=!0,(a=document.createElement("script")).timeout=120,__webpack_require__.nc&&a.setAttribute("nonce",__webpack_require__.nc),a.setAttribute("data-webpack",t+n),a.src=r),e[r]=[o];var u=function(t,o){a.onerror=a.onload=null,clearTimeout(p);var n=e[r];if(delete e[r],a.parentNode&&a.parentNode.removeChild(a),n&&n.forEach(function(e){return e(o)}),t)return t(o)},p=setTimeout(u.bind(null,void 0,{type:"timeout",target:a}),12e4);a.onerror=u.bind(null,a.onerror),a.onload=u.bind(null,a.onload),s&&document.head.appendChild(a)}})(),(()=>{__webpack_require__.r=e=>{"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(e,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(e,"__esModule",{value:!0})}})(),(()=>{var e=[];__webpack_require__.O=(t,r,o,n)=>{if(r){n=n||0;for(var i=e.length;i>0&&e[i-1][2]>n;i--)e[i]=e[i-1];e[i]=[r,o,n];return}for(var a=1/0,i=0;i<e.length;i++){for(var[r,o,n]=e[i],s=!0,l=0;l<r.length;l++)(!1&n||a>=n)&&Object.keys(__webpack_require__.O).every(e=>__webpack_require__.O[e](r[l]))?r.splice(l--,1):(s=!1,n<a&&(a=n));if(s){e.splice(i--,1);var c=o();void 0!==c&&(t=c)}}return t}})(),(()=>{__webpack_require__.p="/bundles/coreshoporder/studio/f5e54e28-fd2c-46c9-b3a1-f3d8fdea2617/"})(),(()=>{__webpack_require__.j="889"})(),(()=>{__webpack_require__.S={},__webpack_require__.initializeSharingData={scopeToSharingDataMapping:{default:[{name:"@coreshop/resource",version:"1.0.0",factory:()=>Promise.all([__webpack_require__.e("476"),__webpack_require__.e("387"),__webpack_require__.e("418"),__webpack_require__.e("468"),__webpack_require__.e("507"),__webpack_require__.e("124"),__webpack_require__.e("70"),__webpack_require__.e("57"),__webpack_require__.e("110"),__webpack_require__.e("459"),__webpack_require__.e("851")]).then(()=>()=>__webpack_require__(5795)),eager:0,singleton:1,requiredVersion:"*",strictVersion:0},{name:"@coreshop/studio-form",version:"1.0.0",factory:()=>Promise.all([__webpack_require__.e("476"),__webpack_require__.e("466"),__webpack_require__.e("468"),__webpack_require__.e("507"),__webpack_require__.e("582"),__webpack_require__.e("57"),__webpack_require__.e("110"),__webpack_require__.e("398"),__webpack_require__.e("663")]).then(()=>()=>__webpack_require__(4935)),eager:0,singleton:1,requiredVersion:"*",strictVersion:0},{name:"@emotion/react",version:"11.14.0",factory:()=>Promise.all([__webpack_require__.e("387"),__webpack_require__.e("57"),__webpack_require__.e("79")]).then(()=>()=>__webpack_require__(3095)),eager:0,singleton:1,requiredVersion:"*",strictVersion:0},{name:"i18next",version:"23.16.8",factory:()=>__webpack_require__.e("760").then(()=>()=>__webpack_require__(5731)),eager:0,singleton:1,requiredVersion:"*",strictVersion:0},{name:"react-dom",version:"18.3.1",factory:()=>Promise.all([__webpack_require__.e("920"),__webpack_require__.e("57")]).then(()=>()=>__webpack_require__(3763)),eager:0,singleton:1,requiredVersion:"*",strictVersion:0},{name:"react-i18next",version:"14.1.3",factory:()=>Promise.all([__webpack_require__.e("63"),__webpack_require__.e("57")]).then(()=>()=>__webpack_require__(2882)),eager:0,singleton:1,requiredVersion:"*",strictVersion:0},{name:"react/jsx-runtime",version:"18.3.1",factory:()=>Promise.all([__webpack_require__.e("57"),__webpack_require__.e("76")]).then(()=>()=>__webpack_require__(9242)),eager:0,singleton:1,requiredVersion:"*",strictVersion:0},{name:"react",version:"18.3.1",factory:()=>__webpack_require__.e("375").then(()=>()=>__webpack_require__(2277)),eager:0,singleton:1,requiredVersion:"*",strictVersion:0},7042]},uniqueName:"coreshoporder"},__webpack_require__.I=__webpack_require__.I||function(){throw Error("should have __webpack_require__.I")}})(),(()=>{__webpack_require__.consumesLoadingData={chunkMapping:{889:["5572"],200:["5782","3933"],110:["5210","2855","3374"],459:["5493"],57:["5168"]},moduleIdToConsumeDataMapping:{3374:{shareScope:"default",shareKey:"react-dom",import:"react-dom",requiredVersion:"*",strictVersion:!1,singleton:!0,eager:!1,fallback:()=>__webpack_require__.e("920").then(()=>()=>__webpack_require__(3763))},5782:{shareScope:"default",shareKey:"@coreshop/studio-form",import:"@coreshop/studio-form",requiredVersion:"*",strictVersion:!1,singleton:!0,eager:!1,fallback:()=>__webpack_require__.e("44").then(()=>()=>__webpack_require__(4935))},5210:{shareScope:"default",shareKey:"react-i18next",import:"react-i18next",requiredVersion:"*",strictVersion:!1,singleton:!0,eager:!1,fallback:()=>__webpack_require__.e("63").then(()=>()=>__webpack_require__(2882))},3933:{shareScope:"default",shareKey:"@coreshop/resource",import:"@coreshop/resource",requiredVersion:"*",strictVersion:!1,singleton:!0,eager:!1,fallback:()=>__webpack_require__.e("232").then(()=>()=>__webpack_require__(5795))},2855:{shareScope:"default",shareKey:"react/jsx-runtime",import:"react/jsx-runtime",requiredVersion:"*",strictVersion:!1,singleton:!0,eager:!1,fallback:()=>__webpack_require__.e("695").then(()=>()=>__webpack_require__(9242))},5572:{shareScope:"default",shareKey:"i18next",import:"i18next",requiredVersion:"*",strictVersion:!1,singleton:!0,eager:!1,fallback:()=>__webpack_require__.e("760").then(()=>()=>__webpack_require__(5731))},5168:{shareScope:"default",shareKey:"react",import:"react",requiredVersion:"*",strictVersion:!1,singleton:!0,eager:!1,fallback:()=>__webpack_require__.e("375").then(()=>()=>__webpack_require__(2277))},5493:{shareScope:"default",shareKey:"@emotion/react",import:"@emotion/react",requiredVersion:"*",strictVersion:!1,singleton:!0,eager:!1,fallback:()=>__webpack_require__.e("460").then(()=>()=>__webpack_require__(3095))}},initialConsumes:["5168","5210","2855","3374","5493","5782","3933","5572"]},__webpack_require__.f.consumes=__webpack_require__.f.consumes||function(){throw Error("should have __webpack_require__.f.consumes")}})(),(()=>{var e={110:0,57:0,889:0};__webpack_require__.f.j=function(t,r){var o=__webpack_require__.o(e,t)?e[t]:void 0;if(0!==o)if(o)r.push(o[2]);else if(/^(110|57)$/.test(t))e[t]=0;else{var n=new Promise((r,n)=>o=e[t]=[r,n]);r.push(o[2]=n);var i=__webpack_require__.p+__webpack_require__.u(t),a=Error(),s=function(r){if(__webpack_require__.o(e,t)&&(0!==(o=e[t])&&(e[t]=void 0),o)){var n=r&&("load"===r.type?"missing":r.type),i=r&&r.target&&r.target.src;a.message="Loading chunk "+t+" failed.\n("+n+": "+i+")",a.name="ChunkLoadError",a.type=n,a.request=i,o[1](a)}};__webpack_require__.l(i,s,"chunk-"+t,t)}},__webpack_require__.O.j=t=>0===e[t];var t=(t,r)=>{var o,n,[i,a,s]=r,l=0;if(i.some(t=>0!==e[t])){for(o in a)__webpack_require__.o(a,o)&&(__webpack_require__.m[o]=a[o]);if(s)var c=s(__webpack_require__)}for(t&&t(r);l<i.length;l++)n=i[l],__webpack_require__.o(e,n)&&e[n]&&e[n][0](),e[n]=0;return __webpack_require__.O(c)},r=self["chunk_coreshoporder "]=self["chunk_coreshoporder "]||[];r.forEach(t.bind(null,0)),r.push=t.bind(null,r.push.bind(r))})(),(()=>{__webpack_require__.remotesLoadingData={chunkMapping:{889:["3090"],200:["2028","3842","4781"],110:["2977"],459:["2696","2703"]},moduleIdToRemoteDataMapping:{2028:{shareScope:"default",name:"./modules/widget-manager",externalModuleId:7042,remoteName:"@pimcore/studio-ui-bundle"},2696:{shareScope:"default",name:"./components",externalModuleId:7042,remoteName:"@pimcore/studio-ui-bundle"},2703:{shareScope:"default",name:"./modules/app",externalModuleId:7042,remoteName:"@pimcore/studio-ui-bundle"},3842:{shareScope:"default",name:"./modules/element",externalModuleId:7042,remoteName:"@pimcore/studio-ui-bundle"},2977:{shareScope:"default",name:".",externalModuleId:7042,remoteName:"@pimcore/studio-ui-bundle"},4781:{shareScope:"default",name:"./app",externalModuleId:7042,remoteName:"@pimcore/studio-ui-bundle"},3090:{shareScope:"default",name:"./modules/data-object",externalModuleId:7042,remoteName:"@pimcore/studio-ui-bundle"}}},__webpack_require__.f.remotes=__webpack_require__.f.remotes||function(){throw Error("should have __webpack_require__.f.remotes")}})(),(()=>{var e=__webpack_require__.x,t=!1;__webpack_require__.x=function(){if(t||(t=!0,__webpack_require__(7559)),"function"==typeof e)return e();console.warn("[MF] Invalid prevStartup")}})();var __webpack_exports__=__webpack_require__.x()})();