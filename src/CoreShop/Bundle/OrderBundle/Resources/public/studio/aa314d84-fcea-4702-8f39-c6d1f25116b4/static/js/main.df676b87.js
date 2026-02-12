/*! For license information please see main.df676b87.js.LICENSE.txt */
(()=>{"use strict";var __webpack_modules__={3888:function(e,t){let r="RUNTIME-001",o="RUNTIME-002",n="RUNTIME-003",i="RUNTIME-004",a="RUNTIME-005",s="RUNTIME-006",l="RUNTIME-007",c="RUNTIME-008",d="TYPE-001",u="BUILD-001",p=e=>{let t=e.split("-")[0].toLowerCase();return`View the docs to see how to solve: https://module-federation.io/guide/troubleshooting/${t}/${e}`},h=(e,t,r,o)=>{let n=[`${[t[e]]} #${e}`];return r&&n.push(`args: ${JSON.stringify(r)}`),n.push(p(e)),o&&n.push(`Original Error Message:
 ${o}`),n.join("\n")};function f(){return(f=Object.assign||function(e){for(var t=1;t<arguments.length;t++){var r=arguments[t];for(var o in r)Object.prototype.hasOwnProperty.call(r,o)&&(e[o]=r[o])}return e}).apply(this,arguments)}let m={[r]:"Failed to get remoteEntry exports.",[o]:'The remote entry interface does not contain "init"',[n]:"Failed to get manifest.",[i]:"Failed to locate remote.",[a]:"Invalid loadShareSync function call from bundler runtime",[s]:"Invalid loadShareSync function call from runtime",[l]:"Failed to get remote snapshot.",[c]:"Failed to load script resources."},g={[d]:"Failed to generate type declaration. Execute the below cmd to reproduce and fix the error."},y={[u]:"Failed to find expose module."},b=f({},m,g,y);t.BUILD_001=u,t.RUNTIME_001=r,t.RUNTIME_002=o,t.RUNTIME_003=n,t.RUNTIME_004=i,t.RUNTIME_005=a,t.RUNTIME_006=s,t.RUNTIME_007=l,t.RUNTIME_008=c,t.TYPE_001=d,t.buildDescMap=y,t.errorDescMap=b,t.getShortErrorMsg=h,t.runtimeDescMap=m,t.typeDescMap=g},5976:function(e,t,r){var o=r(9844),n=r(6012),i=r(3888);let a="[ Federation Runtime ]",s=n.createLogger(a);function l(e,t){e||c(t)}function c(e){if(e instanceof Error)throw e.message=`${a}: ${e.message}`,e;throw Error(`${a}: ${e}`)}function d(e){e instanceof Error&&(e.message=`${a}: ${e.message}`),s.warn(e)}function u(e,t){return -1===e.findIndex(e=>e===t)&&e.push(t),e}function p(e){return"version"in e&&e.version?`${e.name}:${e.version}`:"entry"in e&&e.entry?`${e.name}:${e.entry}`:`${e.name}`}function h(e){return void 0!==e.entry}function f(e){return!e.entry.includes(".json")&&e.entry.includes(".js")}async function m(e,t){try{return await e()}catch(e){t||d(e);return}}function g(e){return e&&"object"==typeof e}let y=Object.prototype.toString;function b(e){return"[object Object]"===y.call(e)}function _(e,t){let r=/^(https?:)?\/\//i;return e.replace(r,"").replace(/\/$/,"")===t.replace(r,"").replace(/\/$/,"")}function E(e){return Array.isArray(e)?e:[e]}function v(e){let t={url:"",type:"global",globalName:""};return n.isBrowserEnv()||n.isReactNativeEnv()?"remoteEntry"in e?{url:e.remoteEntry,type:e.remoteEntryType,globalName:e.globalName}:t:"ssrRemoteEntry"in e?{url:e.ssrRemoteEntry||t.url,type:e.ssrRemoteEntryType||t.type,globalName:e.globalName}:t}let S=(e,t)=>{let r;return r=e.endsWith("/")?e.slice(0,-1):e,t.startsWith(".")&&(t=t.slice(1)),r+=t},x="object"==typeof globalThis?globalThis:window,w=(()=>{try{return document.defaultView}catch(e){return x}})(),$=w;function R(e,t,r){Object.defineProperty(e,t,{value:r,configurable:!1,writable:!0})}function I(e,t){return Object.hasOwnProperty.call(e,t)}I(x,"__GLOBAL_LOADING_REMOTE_ENTRY__")||R(x,"__GLOBAL_LOADING_REMOTE_ENTRY__",{});let T=x.__GLOBAL_LOADING_REMOTE_ENTRY__;function N(e){var t,r,o,n,i,a,s,l,c,d,u,p;I(e,"__VMOK__")&&!I(e,"__FEDERATION__")&&R(e,"__FEDERATION__",e.__VMOK__),I(e,"__FEDERATION__")||(R(e,"__FEDERATION__",{__GLOBAL_PLUGIN__:[],__INSTANCES__:[],moduleInfo:{},__SHARE__:{},__MANIFEST_LOADING__:{},__PRELOADED_MAP__:new Map}),R(e,"__VMOK__",e.__FEDERATION__)),null!=(s=(t=e.__FEDERATION__).__GLOBAL_PLUGIN__)||(t.__GLOBAL_PLUGIN__=[]),null!=(l=(r=e.__FEDERATION__).__INSTANCES__)||(r.__INSTANCES__=[]),null!=(c=(o=e.__FEDERATION__).moduleInfo)||(o.moduleInfo={}),null!=(d=(n=e.__FEDERATION__).__SHARE__)||(n.__SHARE__={}),null!=(u=(i=e.__FEDERATION__).__MANIFEST_LOADING__)||(i.__MANIFEST_LOADING__={}),null!=(p=(a=e.__FEDERATION__).__PRELOADED_MAP__)||(a.__PRELOADED_MAP__=new Map)}function O(){x.__FEDERATION__.__GLOBAL_PLUGIN__=[],x.__FEDERATION__.__INSTANCES__=[],x.__FEDERATION__.moduleInfo={},x.__FEDERATION__.__SHARE__={},x.__FEDERATION__.__MANIFEST_LOADING__={},Object.keys(T).forEach(e=>{delete T[e]})}function k(e){x.__FEDERATION__.__INSTANCES__.push(e)}function A(){return x.__FEDERATION__.__DEBUG_CONSTRUCTOR__}function M(e){let t=arguments.length>1&&void 0!==arguments[1]?arguments[1]:n.isDebugMode();t&&(x.__FEDERATION__.__DEBUG_CONSTRUCTOR__=e,x.__FEDERATION__.__DEBUG_CONSTRUCTOR_VERSION__="0.13.1")}function P(e,t){if("string"==typeof t){if(e[t])return{value:e[t],key:t};for(let r of Object.keys(e)){let[o,n]=r.split(":"),i=`${o}:${t}`,a=e[i];if(a)return{value:a,key:i}}return{value:void 0,key:t}}throw Error("key must be string")}N(x),N(w);let C=()=>w.__FEDERATION__.moduleInfo,D=(e,t)=>{let r=P(t,p(e)).value;if(r&&!r.version&&"version"in e&&e.version&&(r.version=e.version),r)return r;if("version"in e&&e.version){let{version:t}=e,r=p(o._object_without_properties_loose(e,["version"])),n=P(w.__FEDERATION__.moduleInfo,r).value;if((null==n?void 0:n.version)===t)return n}},j=e=>D(e,w.__FEDERATION__.moduleInfo),L=(e,t)=>{let r=p(e);return w.__FEDERATION__.moduleInfo[r]=t,w.__FEDERATION__.moduleInfo},H=e=>(w.__FEDERATION__.moduleInfo=o._extends({},w.__FEDERATION__.moduleInfo,e),()=>{for(let t of Object.keys(e))delete w.__FEDERATION__.moduleInfo[t]}),F=(e,t)=>{let r=t||`__FEDERATION_${e}:custom__`,o=x[r];return{remoteEntryKey:r,entryExports:o}},U=e=>{let{__GLOBAL_PLUGIN__:t}=w.__FEDERATION__;e.forEach(e=>{-1===t.findIndex(t=>t.name===e.name)?t.push(e):d(`The plugin ${e.name} has been registered.`)})},B=()=>w.__FEDERATION__.__GLOBAL_PLUGIN__,V=e=>x.__FEDERATION__.__PRELOADED_MAP__.get(e),G=e=>x.__FEDERATION__.__PRELOADED_MAP__.set(e,!0),q="default",z="global",W="[0-9A-Za-z-]+",K=`(?:\\+(${W}(?:\\.${W})*))`,Y="0|[1-9]\\d*",J="[0-9]+",X="\\d*[a-zA-Z-][a-zA-Z0-9-]*",Z=`(?:${J}|${X})`,Q=`(?:-?(${Z}(?:\\.${Z})*))`,ee=`(?:${Y}|${X})`,et=`(?:-(${ee}(?:\\.${ee})*))`,er=`${Y}|x|X|\\*`,eo=`[v=\\s]*(${er})(?:\\.(${er})(?:\\.(${er})(?:${et})?${K}?)?)?`,en=`^\\s*(${eo})\\s+-\\s+(${eo})\\s*$`,ei=`(${J})\\.(${J})\\.(${J})`,ea=`[v=\\s]*${ei}${Q}?${K}?`,es="((?:<|>)?=?)",el=`(\\s*)${es}\\s*(${ea}|${eo})`,ec="(?:~>?)",ed=`(\\s*)${ec}\\s+`,eu="(?:\\^)",ep=`(\\s*)${eu}\\s+`,eh="(<|>)?=?\\s*\\*",ef=`^${eu}${eo}$`,em=`(${Y})\\.(${Y})\\.(${Y})`,eg=`v?${em}${et}?${K}?`,ey=`^${ec}${eo}$`,eb=`^${es}\\s*${eo}$`,e_=`^${es}\\s*(${eg})$|^$`,eE="^\\s*>=\\s*0.0.0\\s*$";function ev(e){return new RegExp(e)}function eS(e){return!e||"x"===e.toLowerCase()||"*"===e}function ex(){for(var e=arguments.length,t=Array(e),r=0;r<e;r++)t[r]=arguments[r];return e=>t.reduce((e,t)=>t(e),e)}function ew(e){return e.match(ev(e_))}function e$(e,t,r,o){let n=`${e}.${t}.${r}`;return o?`${n}-${o}`:n}function eR(e){return e.replace(ev(en),(e,t,r,o,n,i,a,s,l,c,d,u)=>(t=eS(r)?"":eS(o)?`>=${r}.0.0`:eS(n)?`>=${r}.${o}.0`:`>=${t}`,s=eS(l)?"":eS(c)?`<${Number(l)+1}.0.0-0`:eS(d)?`<${l}.${Number(c)+1}.0-0`:u?`<=${l}.${c}.${d}-${u}`:`<=${s}`,`${t} ${s}`.trim()))}function eI(e){return e.replace(ev(el),"$1$2$3")}function eT(e){return e.replace(ev(ed),"$1~")}function eN(e){return e.replace(ev(ep),"$1^")}function eO(e){return e.trim().split(/\s+/).map(e=>e.replace(ev(ef),(e,t,r,o,n)=>{if(eS(t))return"";if(eS(r))return`>=${t}.0.0 <${Number(t)+1}.0.0-0`;if(eS(o))if("0"===t)return`>=${t}.${r}.0 <${t}.${Number(r)+1}.0-0`;else return`>=${t}.${r}.0 <${Number(t)+1}.0.0-0`;if(n)if("0"!==t)return`>=${t}.${r}.${o}-${n} <${Number(t)+1}.0.0-0`;else if("0"===r)return`>=${t}.${r}.${o}-${n} <${t}.${r}.${Number(o)+1}-0`;else return`>=${t}.${r}.${o}-${n} <${t}.${Number(r)+1}.0-0`;if("0"===t)if("0"===r)return`>=${t}.${r}.${o} <${t}.${r}.${Number(o)+1}-0`;else return`>=${t}.${r}.${o} <${t}.${Number(r)+1}.0-0`;return`>=${t}.${r}.${o} <${Number(t)+1}.0.0-0`})).join(" ")}function ek(e){return e.trim().split(/\s+/).map(e=>e.replace(ev(ey),(e,t,r,o,n)=>eS(t)?"":eS(r)?`>=${t}.0.0 <${Number(t)+1}.0.0-0`:eS(o)?`>=${t}.${r}.0 <${t}.${Number(r)+1}.0-0`:n?`>=${t}.${r}.${o}-${n} <${t}.${Number(r)+1}.0-0`:`>=${t}.${r}.${o} <${t}.${Number(r)+1}.0-0`)).join(" ")}function eA(e){return e.split(/\s+/).map(e=>e.trim().replace(ev(eb),(e,t,r,o,n,i)=>{let a=eS(r),s=a||eS(o),l=s||eS(n);if("="===t&&l&&(t=""),i="",a)if(">"===t||"<"===t)return"<0.0.0-0";else return"*";return t&&l?(s&&(o=0),n=0,">"===t?(t=">=",s?(r=Number(r)+1,o=0):o=Number(o)+1,n=0):"<="===t&&(t="<",s?r=Number(r)+1:o=Number(o)+1),"<"===t&&(i="-0"),`${t+r}.${o}.${n}${i}`):s?`>=${r}.0.0${i} <${Number(r)+1}.0.0-0`:l?`>=${r}.${o}.0${i} <${r}.${Number(o)+1}.0-0`:e})).join(" ")}function eM(e){return e.trim().replace(ev(eh),"")}function eP(e){return e.trim().replace(ev(eE),"")}function eC(e,t){return(e=Number(e)||e)>(t=Number(t)||t)?1:e===t?0:-1}function eD(e,t){let{preRelease:r}=e,{preRelease:o}=t;if(void 0===r&&o)return 1;if(r&&void 0===o)return -1;if(void 0===r&&void 0===o)return 0;for(let e=0,t=r.length;e<=t;e++){let t=r[e],n=o[e];if(t!==n){if(void 0===t&&void 0===n)return 0;if(!t)return 1;if(!n)return -1;return eC(t,n)}}return 0}function ej(e,t){return eC(e.major,t.major)||eC(e.minor,t.minor)||eC(e.patch,t.patch)||eD(e,t)}function eL(e,t){return e.version===t.version}function eH(e,t){switch(e.operator){case"":case"=":return eL(e,t);case">":return 0>ej(e,t);case">=":return eL(e,t)||0>ej(e,t);case"<":return ej(e,t)>0;case"<=":return eL(e,t)||ej(e,t)>0;case void 0:return!0;default:return!1}}function eF(e){return ex(eO,ek,eA,eM)(e)}function eU(e){return ex(eR,eI,eT,eN)(e.trim()).split(/\s+/).join(" ")}function eB(e,t){if(!e)return!1;let r=eU(t).split(" ").map(e=>eF(e)).join(" ").split(/\s+/).map(e=>eP(e)),o=ew(e);if(!o)return!1;let[,n,,i,a,s,l]=o,c={operator:n,version:e$(i,a,s,l),major:i,minor:a,patch:s,preRelease:null==l?void 0:l.split(".")};for(let e of r){let t=ew(e);if(!t)return!1;let[,r,,o,n,i,a]=t;if(!eH({operator:r,version:e$(o,n,i,a),major:o,minor:n,patch:i,preRelease:null==a?void 0:a.split(".")},c))return!1}return!0}function eV(e,t,r,n){var i,a,s;let l;return l="get"in e?e.get:"lib"in e?()=>Promise.resolve(e.lib):()=>Promise.resolve(()=>{throw Error(`Can not get shared '${r}'!`)}),o._extends({deps:[],useIn:[],from:t,loading:null},e,{shareConfig:o._extends({requiredVersion:`^${e.version}`,singleton:!1,eager:!1,strictVersion:!1},e.shareConfig),get:l,loaded:null!=e&&!!e.loaded||"lib"in e||void 0,version:null!=(i=e.version)?i:"0",scope:Array.isArray(e.scope)?e.scope:[null!=(a=e.scope)?a:"default"],strategy:(null!=(s=e.strategy)?s:n)||"version-first"})}function eG(e,t){let r=t.shared||{},n=t.name,i=Object.keys(r).reduce((e,o)=>{let i=E(r[o]);return e[o]=e[o]||[],i.forEach(r=>{e[o].push(eV(r,n,o,t.shareStrategy))}),e},{}),a=o._extends({},e.shared);return Object.keys(i).forEach(e=>{a[e]?i[e].forEach(t=>{a[e].find(e=>e.version===t.version)||a[e].push(t)}):a[e]=i[e]}),{shared:a,shareInfos:i}}function eq(e,t){let r=e=>{if(!Number.isNaN(Number(e))){let t=e.split("."),r=e;for(let e=0;e<3-t.length;e++)r+=".0";return r}return e};return!!eB(r(e),`<=${r(t)}`)}let ez=(e,t)=>{let r=t||function(e,t){return eq(e,t)};return Object.keys(e).reduce((e,t)=>!e||r(e,t)||"0"===e?t:e,0)},eW=e=>!!e.loaded||"function"==typeof e.lib,eK=e=>!!e.loading;function eY(e,t,r){let o=e[t][r],n=function(e,t){return!eW(o[e])&&eq(e,t)};return ez(e[t][r],n)}function eJ(e,t,r){let o=e[t][r],n=function(e,t){let r=e=>eW(e)||eK(e);if(r(o[t]))if(r(o[e]))return!!eq(e,t);else return!0;return!r(o[e])&&eq(e,t)};return ez(e[t][r],n)}function eX(e){return"loaded-first"===e?eJ:eY}function eZ(e,t,r,o){if(!e)return;let{shareConfig:n,scope:i=q,strategy:a}=r;for(let s of Array.isArray(i)?i:[i])if(n&&e[s]&&e[s][t]){let{requiredVersion:i}=n,l=eX(a)(e,s,t),u=()=>{if(n.singleton){if("string"==typeof i&&!eB(l,i)){let o=`Version ${l} from ${l&&e[s][t][l].from} of shared singleton module ${t} does not satisfy the requirement of ${r.from} which needs ${i})`;n.strictVersion?c(o):d(o)}return e[s][t][l]}if(!1===i||"*"===i||eB(l,i))return e[s][t][l];for(let[r,o]of Object.entries(e[s][t]))if(eB(r,i))return o},p={shareScopeMap:e,scope:s,pkgName:t,version:l,GlobalFederation:$.__FEDERATION__,resolver:u};return(o.emit(p)||p).resolver()}}function eQ(){return $.__FEDERATION__.__SHARE__}function e0(e){var t;let{pkgName:r,extraOptions:o,shareInfos:n}=e,i=e=>{if(!e)return;let t={};e.forEach(e=>{t[e.version]=e});let r=function(e,r){return!eW(t[e])&&eq(e,r)},o=ez(t,r);return t[o]};return Object.assign({},(null!=(t=null==o?void 0:o.resolver)?t:i)(n[r]),null==o?void 0:o.customShareInfo)}var e1={global:{Global:$,nativeGlobal:w,resetFederationGlobalInfo:O,setGlobalFederationInstance:k,getGlobalFederationConstructor:A,setGlobalFederationConstructor:M,getInfoWithoutType:P,getGlobalSnapshot:C,getTargetSnapshotInfoByModuleInfo:D,getGlobalSnapshotInfoByModuleInfo:j,setGlobalSnapshotInfoByModuleInfo:L,addGlobalSnapshot:H,getRemoteEntryExports:F,registerGlobalPlugins:U,getGlobalHostPlugins:B,getPreloaded:V,setPreloaded:G},share:{getRegisteredShare:eZ,getGlobalShareScope:eQ}};function e2(){return"coreshoporder:1.0.0"}function e5(e,t){for(let r of e){let e=t.startsWith(r.name),o=t.replace(r.name,"");if(e){if(o.startsWith("/"))return{pkgNameOrAlias:r.name,expose:o=`.${o}`,remote:r};else if(""===o)return{pkgNameOrAlias:r.name,expose:".",remote:r}}let n=r.alias&&t.startsWith(r.alias),i=r.alias&&t.replace(r.alias,"");if(r.alias&&n){if(i&&i.startsWith("/"))return{pkgNameOrAlias:r.alias,expose:i=`.${i}`,remote:r};else if(""===i)return{pkgNameOrAlias:r.alias,expose:".",remote:r}}}}function e6(e,t){for(let r of e)if(t===r.name||r.alias&&t===r.alias)return r}function e8(e,t){let r=B();return r.length>0&&r.forEach(t=>{(null==e?void 0:e.find(e=>e.name!==t.name))&&e.push(t)}),e&&e.length>0&&e.forEach(e=>{t.forEach(t=>{t.applyPlugin(e)})}),e}async function e4(e){let{entry:t,remoteEntryExports:r}=e;return new Promise((e,o)=>{try{r?e(r):"undefined"!=typeof FEDERATION_ALLOW_NEW_FUNCTION?Function("callbacks",`import("${t}").then(callbacks[0]).catch(callbacks[1])`)([e,o]):import(t).then(e).catch(o)}catch(e){o(e)}})}async function e3(e){let{entry:t,remoteEntryExports:r}=e;return new Promise((e,o)=>{try{r?e(r):Function("callbacks",`System.import("${t}").then(callbacks[0]).catch(callbacks[1])`)([e,o])}catch(e){o(e)}})}async function e9(e){let{name:t,globalName:r,entry:o,loaderHook:a}=e,{entryExports:s}=F(t,r);return s||n.loadScript(o,{attrs:{},createScriptHook:(e,t)=>{let r=a.lifecycle.createScript.emit({url:e,attrs:t});if(r&&(r instanceof HTMLScriptElement||"script"in r||"timeout"in r))return r}}).then(()=>{let{remoteEntryKey:e,entryExports:n}=F(t,r);return l(n,i.getShortErrorMsg(i.RUNTIME_001,i.runtimeDescMap,{remoteName:t,remoteEntryUrl:o,remoteEntryKey:e})),n}).catch(e=>{throw l(void 0,i.getShortErrorMsg(i.RUNTIME_008,i.runtimeDescMap,{remoteName:t,resourceUrl:o})),e})}async function e7(e){let{remoteInfo:t,remoteEntryExports:r,loaderHook:o}=e,{entry:n,entryGlobalName:i,name:a,type:s}=t;switch(s){case"esm":case"module":return e4({entry:n,remoteEntryExports:r});case"system":return e3({entry:n,remoteEntryExports:r});default:return e9({entry:n,globalName:i,name:a,loaderHook:o})}}async function te(e){let{remoteInfo:t,loaderHook:r}=e,{entry:o,entryGlobalName:a,name:s,type:c}=t,{entryExports:d}=F(s,a);return d||n.loadScriptNode(o,{attrs:{name:s,globalName:a,type:c},loaderHook:{createScriptHook:function(e){let t=arguments.length>1&&void 0!==arguments[1]?arguments[1]:{},o=r.lifecycle.createScript.emit({url:e,attrs:t});if(o&&"url"in o)return o}}}).then(()=>{let{remoteEntryKey:e,entryExports:t}=F(s,a);return l(t,i.getShortErrorMsg(i.RUNTIME_001,i.runtimeDescMap,{remoteName:s,remoteEntryUrl:o,remoteEntryKey:e})),t}).catch(e=>{throw e})}function tt(e){let{entry:t,name:r}=e;return n.composeKeyWithSeparator(r,t)}async function tr(e){let{origin:t,remoteEntryExports:r,remoteInfo:o}=e,i=tt(o);if(r)return r;if(!T[i]){let e=t.remoteHandler.hooks.lifecycle.loadEntry,a=t.loaderHook;T[i]=e.emit({loaderHook:a,remoteInfo:o,remoteEntryExports:r}).then(e=>e||(n.isBrowserEnv()?e7({remoteInfo:o,remoteEntryExports:r,loaderHook:a}):te({remoteInfo:o,loaderHook:a})))}return T[i]}function to(e){return o._extends({},e,{entry:"entry"in e?e.entry:"",type:e.type||z,entryGlobalName:e.entryGlobalName||e.name,shareScope:e.shareScope||q})}let tn=class{async getEntry(){let e;if(this.remoteEntryExports)return this.remoteEntryExports;try{e=await tr({origin:this.host,remoteInfo:this.remoteInfo,remoteEntryExports:this.remoteEntryExports})}catch(r){let t=tt(this.remoteInfo);e=await this.host.loaderHook.lifecycle.loadEntryError.emit({getRemoteEntry:tr,origin:this.host,remoteInfo:this.remoteInfo,remoteEntryExports:this.remoteEntryExports,globalLoading:T,uniqueKey:t})}return l(e,`remoteEntryExports is undefined 
 ${n.safeToString(this.remoteInfo)}`),this.remoteEntryExports=e,this.remoteEntryExports}async get(e,t,r,n){let a,{loadFactory:s=!0}=r||{loadFactory:!0},d=await this.getEntry();if(!this.inited){let t=this.host.shareScopeMap,r=Array.isArray(this.remoteInfo.shareScope)?this.remoteInfo.shareScope:[this.remoteInfo.shareScope];r.length||r.push("default"),r.forEach(e=>{t[e]||(t[e]={})});let a=t[r[0]],s=[],l={version:this.remoteInfo.version||"",shareScopeKeys:Array.isArray(this.remoteInfo.shareScope)?r:this.remoteInfo.shareScope||"default"};Object.defineProperty(l,"shareScopeMap",{value:t,enumerable:!1});let u=await this.host.hooks.lifecycle.beforeInitContainer.emit({shareScope:a,remoteEntryInitOptions:l,initScope:s,remoteInfo:this.remoteInfo,origin:this.host});void 0===(null==d?void 0:d.init)&&c(i.getShortErrorMsg(i.RUNTIME_002,i.runtimeDescMap,{remoteName:name,remoteEntryUrl:this.remoteInfo.entry,remoteEntryKey:this.remoteInfo.entryGlobalName})),await d.init(u.shareScope,u.initScope,u.remoteEntryInitOptions),await this.host.hooks.lifecycle.initContainer.emit(o._extends({},u,{id:e,remoteSnapshot:n,remoteEntryExports:d}))}this.lib=d,this.inited=!0,(a=await this.host.loaderHook.lifecycle.getModuleFactory.emit({remoteEntryExports:d,expose:t,moduleInfo:this.remoteInfo}))||(a=await d.get(t)),l(a,`${p(this.remoteInfo)} remote don't export ${t}.`);let u=S(this.remoteInfo.name,t),h=this.wraperFactory(a,u);return s?await h():h}wraperFactory(e,t){function r(e,t){e&&"object"==typeof e&&Object.isExtensible(e)&&!Object.getOwnPropertyDescriptor(e,Symbol.for("mf_module_id"))&&Object.defineProperty(e,Symbol.for("mf_module_id"),{value:t,enumerable:!1})}return e instanceof Promise?async()=>{let o=await e();return r(o,t),o}:()=>{let o=e();return r(o,t),o}}constructor({remoteInfo:e,host:t}){this.inited=!1,this.lib=void 0,this.remoteInfo=e,this.host=t}};class ti{on(e){"function"==typeof e&&this.listeners.add(e)}once(e){let t=this;this.on(function r(){for(var o=arguments.length,n=Array(o),i=0;i<o;i++)n[i]=arguments[i];return t.remove(r),e.apply(null,n)})}emit(){let e;for(var t=arguments.length,r=Array(t),o=0;o<t;o++)r[o]=arguments[o];return this.listeners.size>0&&this.listeners.forEach(t=>{e=t(...r)}),e}remove(e){this.listeners.delete(e)}removeAll(){this.listeners.clear()}constructor(e){this.type="",this.listeners=new Set,e&&(this.type=e)}}class ta extends ti{emit(){let e;for(var t=arguments.length,r=Array(t),o=0;o<t;o++)r[o]=arguments[o];let n=Array.from(this.listeners);if(n.length>0){let t=0,o=e=>!1!==e&&(t<n.length?Promise.resolve(n[t++].apply(null,r)).then(o):e);e=o()}return Promise.resolve(e)}}function ts(e,t){if(!g(t))return!1;if(e!==t){for(let r in e)if(!(r in t))return!1}return!0}class tl extends ti{emit(e){for(let t of(g(e)||c(`The data for the "${this.type}" hook should be an object.`),this.listeners))try{let r=t(e);if(ts(e,r))e=r;else{this.onerror(`A plugin returned an unacceptable value for the "${this.type}" type.`);break}}catch(e){d(e),this.onerror(e)}return e}constructor(e){super(),this.onerror=c,this.type=e}}class tc extends ti{emit(e){g(e)||c(`The response data for the "${this.type}" hook must be an object.`);let t=Array.from(this.listeners);if(t.length>0){let r=0,o=t=>(d(t),this.onerror(t),e),n=i=>{if(ts(e,i)){if(e=i,r<t.length)try{return Promise.resolve(t[r++](e)).then(n,o)}catch(e){return o(e)}}else this.onerror(`A plugin returned an incorrect value for the "${this.type}" type.`);return e};return Promise.resolve(n(e))}return Promise.resolve(e)}constructor(e){super(),this.onerror=c,this.type=e}}class td{applyPlugin(e){l(b(e),"Plugin configuration is invalid.");let t=e.name;l(t,"A name must be provided by the plugin."),this.registerPlugins[t]||(this.registerPlugins[t]=e,Object.keys(this.lifecycle).forEach(t=>{let r=e[t];r&&this.lifecycle[t].on(r)}))}removePlugin(e){l(e,"A name is required.");let t=this.registerPlugins[e];l(t,`The plugin "${e}" is not registered.`),Object.keys(t).forEach(e=>{"name"!==e&&this.lifecycle[e].remove(t[e])})}inherit(e){let{lifecycle:t,registerPlugins:r}=e;Object.keys(t).forEach(e=>{l(!this.lifecycle[e],`The hook "${e}" has a conflict and cannot be inherited.`),this.lifecycle[e]=t[e]}),Object.keys(r).forEach(e=>{l(!this.registerPlugins[e],`The plugin "${e}" has a conflict and cannot be inherited.`),this.applyPlugin(r[e])})}constructor(e){this.registerPlugins={},this.lifecycle=e,this.lifecycleKeys=Object.keys(e)}}function tu(e){return o._extends({resourceCategory:"sync",share:!0,depsRemote:!0,prefetchInterface:!1},e)}function tp(e,t){return t.map(t=>{let r=e6(e,t.nameOrAlias);return l(r,`Unable to preload ${t.nameOrAlias} as it is not included in ${!r&&n.safeToString({remoteInfo:r,remotes:e})}`),{remote:r,preloadConfig:tu(t)}})}function th(e){return e?e.map(e=>"."===e?e:e.startsWith("./")?e.replace("./",""):e):[]}function tf(e,t,r){let o=!(arguments.length>3)||void 0===arguments[3]||arguments[3],{cssAssets:i,jsAssetsWithoutEntry:a,entryAssets:s}=r;if(t.options.inBrowser){if(s.forEach(r=>{let{moduleInfo:o}=r,n=t.moduleCache.get(e.name);n?tr({origin:t,remoteInfo:o,remoteEntryExports:n.remoteEntryExports}):tr({origin:t,remoteInfo:o,remoteEntryExports:void 0})}),o){let e={rel:"preload",as:"style"};i.forEach(r=>{let{link:o,needAttach:i}=n.createLink({url:r,cb:()=>{},attrs:e,createLinkHook:(e,r)=>{let o=t.loaderHook.lifecycle.createLink.emit({url:e,attrs:r});if(o instanceof HTMLLinkElement)return o}});i&&document.head.appendChild(o)})}else{let e={rel:"stylesheet",type:"text/css"};i.forEach(r=>{let{link:o,needAttach:i}=n.createLink({url:r,cb:()=>{},attrs:e,createLinkHook:(e,r)=>{let o=t.loaderHook.lifecycle.createLink.emit({url:e,attrs:r});if(o instanceof HTMLLinkElement)return o},needDeleteLink:!1});i&&document.head.appendChild(o)})}if(o){let e={rel:"preload",as:"script"};a.forEach(r=>{let{link:o,needAttach:i}=n.createLink({url:r,cb:()=>{},attrs:e,createLinkHook:(e,r)=>{let o=t.loaderHook.lifecycle.createLink.emit({url:e,attrs:r});if(o instanceof HTMLLinkElement)return o}});i&&document.head.appendChild(o)})}else{let r={fetchpriority:"high",type:(null==e?void 0:e.type)==="module"?"module":"text/javascript"};a.forEach(e=>{let{script:o,needAttach:i}=n.createScript({url:e,cb:()=>{},attrs:r,createScriptHook:(e,r)=>{let o=t.loaderHook.lifecycle.createScript.emit({url:e,attrs:r});if(o instanceof HTMLScriptElement)return o},needDeleteScript:!0});i&&document.head.appendChild(o)})}}}function tm(e,t){let r=v(t);r.url||c(`The attribute remoteEntry of ${e.name} must not be undefined.`);let o=n.getResourceUrl(t,r.url);n.isBrowserEnv()||o.startsWith("http")||(o=`https:${o}`),e.type=r.type,e.entryGlobalName=r.globalName,e.entry=o,e.version=t.version,e.buildVersion=t.buildVersion}function tg(){return{name:"snapshot-plugin",async afterResolve(e){let{remote:t,pkgNameOrAlias:r,expose:n,origin:i,remoteInfo:a}=e;if(!h(t)||!f(t)){let{remoteSnapshot:s,globalSnapshot:l}=await i.snapshotHandler.loadRemoteSnapshotInfo(t);tm(a,s);let c={remote:t,preloadConfig:{nameOrAlias:r,exposes:[n],resourceCategory:"sync",share:!1,depsRemote:!1}},d=await i.remoteHandler.hooks.lifecycle.generatePreloadAssets.emit({origin:i,preloadOptions:c,remoteInfo:a,remote:t,remoteSnapshot:s,globalSnapshot:l});return d&&tf(a,i,d,!1),o._extends({},e,{remoteSnapshot:s})}return e}}}function ty(e){let t=e.split(":");return 1===t.length?{name:t[0],version:void 0}:2===t.length?{name:t[0],version:t[1]}:{name:t[1],version:t[2]}}function tb(e,t,r,o){let i=arguments.length>4&&void 0!==arguments[4]?arguments[4]:{},a=arguments.length>5?arguments[5]:void 0,{value:s}=P(e,p(t)),l=a||s;if(l&&!n.isManifestProvider(l)&&(r(l,t,o),l.remotesInfo))for(let t of Object.keys(l.remotesInfo)){if(i[t])continue;i[t]=!0;let o=ty(t),n=l.remotesInfo[t];tb(e,{name:o.name,version:n.matchedVersion},r,!1,i,void 0)}}let t_=(e,t)=>document.querySelector(`${e}[${"link"===e?"href":"src"}="${t}"]`);function tE(e,t,r,o,i){let a=[],s=[],l=[],c=new Set,d=new Set,{options:u}=e,{preloadConfig:p}=t,{depsRemote:h}=p;if(tb(o,r,(t,r,o)=>{let i;if(o)i=p;else if(Array.isArray(h)){let e=h.find(e=>e.nameOrAlias===r.name||e.nameOrAlias===r.alias);if(!e)return;i=tu(e)}else{if(!0!==h)return;i=p}let c=n.getResourceUrl(t,v(t).url);c&&l.push({name:r.name,moduleInfo:{name:r.name,entry:c,type:"remoteEntryType"in t?t.remoteEntryType:"global",entryGlobalName:"globalName"in t?t.globalName:r.name,shareScope:"",version:"version"in t?t.version:void 0},url:c});let d="modules"in t?t.modules:[],u=th(i.exposes);if(u.length&&"modules"in t){var f;d=null==t||null==(f=t.modules)?void 0:f.reduce((e,t)=>((null==u?void 0:u.indexOf(t.moduleName))!==-1&&e.push(t),e),[])}function m(e){let r=e.map(e=>n.getResourceUrl(t,e));return i.filter?r.filter(i.filter):r}if(d){let o=d.length;for(let n=0;n<o;n++){let o=d[n],l=`${r.name}/${o.moduleName}`;e.remoteHandler.hooks.lifecycle.handlePreloadModule.emit({id:"."===o.moduleName?r.name:l,name:r.name,remoteSnapshot:t,preloadConfig:i,remote:r,origin:e}),V(l)||("all"===i.resourceCategory?(a.push(...m(o.assets.css.async)),a.push(...m(o.assets.css.sync)),s.push(...m(o.assets.js.async))):(i.resourceCategory="sync",a.push(...m(o.assets.css.sync))),s.push(...m(o.assets.js.sync)),G(l))}}},!0,{},i),i.shared){let t=(t,r)=>{let o=eZ(e.shareScopeMap,r.sharedName,t,e.sharedHandler.hooks.lifecycle.resolveShare);o&&"function"==typeof o.lib&&(r.assets.js.sync.forEach(e=>{c.add(e)}),r.assets.css.sync.forEach(e=>{d.add(e)}))};i.shared.forEach(e=>{var r;let o=null==(r=u.shared)?void 0:r[e.sharedName];if(!o)return;let n=e.version?o.find(t=>t.version===e.version):o;n&&E(n).forEach(r=>{t(r,e)})})}let f=s.filter(e=>!c.has(e)&&!t_("script",e));return{cssAssets:a.filter(e=>!d.has(e)&&!t_("link",e)),jsAssetsWithoutEntry:f,entryAssets:l.filter(e=>!t_("script",e.url))}}let tv=function(){return{name:"generate-preload-assets-plugin",async generatePreloadAssets(e){let{origin:t,preloadOptions:r,remoteInfo:o,remote:i,globalSnapshot:a,remoteSnapshot:s}=e;return n.isBrowserEnv()?h(i)&&f(i)?{cssAssets:[],jsAssetsWithoutEntry:[],entryAssets:[{name:i.name,url:i.entry,moduleInfo:{name:o.name,entry:i.entry,type:o.type||"global",entryGlobalName:"",shareScope:""}}]}:(tm(o,s),tE(t,r,o,a,s)):{cssAssets:[],jsAssetsWithoutEntry:[],entryAssets:[]}}}};function tS(e,t){let r=j({name:t.options.name,version:t.options.version}),o=r&&"remotesInfo"in r&&r.remotesInfo&&P(r.remotesInfo,e.name).value;return o&&o.matchedVersion?{hostGlobalSnapshot:r,globalSnapshot:C(),remoteSnapshot:j({name:e.name,version:o.matchedVersion})}:{hostGlobalSnapshot:void 0,globalSnapshot:C(),remoteSnapshot:j({name:e.name,version:"version"in e?e.version:void 0})}}class tx{async loadSnapshot(e){let{options:t}=this.HostInstance,{hostGlobalSnapshot:r,remoteSnapshot:o,globalSnapshot:n}=this.getGlobalRemoteInfo(e),{remoteSnapshot:i,globalSnapshot:a}=await this.hooks.lifecycle.loadSnapshot.emit({options:t,moduleInfo:e,hostGlobalSnapshot:r,remoteSnapshot:o,globalSnapshot:n});return{remoteSnapshot:i,globalSnapshot:a}}async loadRemoteSnapshotInfo(e){let t,r,{options:a}=this.HostInstance;await this.hooks.lifecycle.beforeLoadRemoteSnapshot.emit({options:a,moduleInfo:e});let s=j({name:this.HostInstance.options.name,version:this.HostInstance.options.version});s||(s={version:this.HostInstance.options.version||"",remoteEntry:"",remotesInfo:{}},H({[this.HostInstance.options.name]:s})),s&&"remotesInfo"in s&&!P(s.remotesInfo,e.name).value&&("version"in e||"entry"in e)&&(s.remotesInfo=o._extends({},null==s?void 0:s.remotesInfo,{[e.name]:{matchedVersion:"version"in e?e.version:e.entry}}));let{hostGlobalSnapshot:l,remoteSnapshot:d,globalSnapshot:u}=this.getGlobalRemoteInfo(e),{remoteSnapshot:p,globalSnapshot:f}=await this.hooks.lifecycle.loadSnapshot.emit({options:a,moduleInfo:e,hostGlobalSnapshot:l,remoteSnapshot:d,globalSnapshot:u});if(p)if(n.isManifestProvider(p)){let i=n.isBrowserEnv()?p.remoteEntry:p.ssrRemoteEntry||p.remoteEntry||"",a=await this.getManifestJson(i,e,{}),s=L(o._extends({},e,{entry:i}),a);t=a,r=s}else{let{remoteSnapshot:o}=await this.hooks.lifecycle.loadRemoteSnapshot.emit({options:this.HostInstance.options,moduleInfo:e,remoteSnapshot:p,from:"global"});t=o,r=f}else if(h(e)){let o=await this.getManifestJson(e.entry,e,{}),n=L(e,o),{remoteSnapshot:i}=await this.hooks.lifecycle.loadRemoteSnapshot.emit({options:this.HostInstance.options,moduleInfo:e,remoteSnapshot:o,from:"global"});t=i,r=n}else c(i.getShortErrorMsg(i.RUNTIME_007,i.runtimeDescMap,{hostName:e.name,hostVersion:e.version,globalSnapshot:JSON.stringify(f)}));return await this.hooks.lifecycle.afterLoadSnapshot.emit({options:a,moduleInfo:e,remoteSnapshot:t}),{remoteSnapshot:t,globalSnapshot:r}}getGlobalRemoteInfo(e){return tS(e,this.HostInstance)}async getManifestJson(e,t,r){let o=async()=>{let r=this.manifestCache.get(e);if(r)return r;try{let t=await this.loaderHook.lifecycle.fetch.emit(e,{});t&&t instanceof Response||(t=await fetch(e,{})),r=await t.json()}catch(o){(r=await this.HostInstance.remoteHandler.hooks.lifecycle.errorLoadRemote.emit({id:e,error:o,from:"runtime",lifecycle:"afterResolve",origin:this.HostInstance}))||(delete this.manifestLoading[e],c(i.getShortErrorMsg(i.RUNTIME_003,i.runtimeDescMap,{manifestUrl:e,moduleName:t.name,hostName:this.HostInstance.options.name},`${o}`)))}return l(r.metaData&&r.exposes&&r.shared,`${e} is not a federation manifest`),this.manifestCache.set(e,r),r},a=async()=>{let r=await o(),i=n.generateSnapshotFromManifest(r,{version:e}),{remoteSnapshot:a}=await this.hooks.lifecycle.loadRemoteSnapshot.emit({options:this.HostInstance.options,moduleInfo:t,manifestJson:r,remoteSnapshot:i,manifestUrl:e,from:"manifest"});return a};return this.manifestLoading[e]||(this.manifestLoading[e]=a().then(e=>e)),this.manifestLoading[e]}constructor(e){this.loadingHostSnapshot=null,this.manifestCache=new Map,this.hooks=new td({beforeLoadRemoteSnapshot:new ta("beforeLoadRemoteSnapshot"),loadSnapshot:new tc("loadGlobalSnapshot"),loadRemoteSnapshot:new tc("loadRemoteSnapshot"),afterLoadSnapshot:new tc("afterLoadSnapshot")}),this.manifestLoading=$.__FEDERATION__.__MANIFEST_LOADING__,this.HostInstance=e,this.loaderHook=e.loaderHook}}class tw{registerShared(e,t){let{shareInfos:r,shared:o}=eG(e,t);return Object.keys(r).forEach(e=>{r[e].forEach(r=>{!eZ(this.shareScopeMap,e,r,this.hooks.lifecycle.resolveShare)&&r&&r.lib&&this.setShared({pkgName:e,lib:r.lib,get:r.get,loaded:!0,shared:r,from:t.name})})}),{shareInfos:r,shared:o}}async loadShare(e,t){let{host:r}=this,o=e0({pkgName:e,extraOptions:t,shareInfos:r.options.shared});(null==o?void 0:o.scope)&&await Promise.all(o.scope.map(async e=>{await Promise.all(this.initializeSharing(e,{strategy:o.strategy}))}));let{shareInfo:n}=await this.hooks.lifecycle.beforeLoadShare.emit({pkgName:e,shareInfo:o,shared:r.options.shared,origin:r});l(n,`Cannot find ${e} Share in the ${r.options.name}. Please ensure that the ${e} Share parameters have been injected`);let i=eZ(this.shareScopeMap,e,n,this.hooks.lifecycle.resolveShare),a=e=>{e.useIn||(e.useIn=[]),u(e.useIn,r.options.name)};if(i&&i.lib)return a(i),i.lib;if(i&&i.loading&&!i.loaded){let e=await i.loading;return i.loaded=!0,i.lib||(i.lib=e),a(i),e}if(i){let t=(async()=>{let t=await i.get();n.lib=t,n.loaded=!0,a(n);let r=eZ(this.shareScopeMap,e,n,this.hooks.lifecycle.resolveShare);return r&&(r.lib=t,r.loaded=!0),t})();return this.setShared({pkgName:e,loaded:!1,shared:i,from:r.options.name,lib:null,loading:t}),t}{if(null==t?void 0:t.customShareInfo)return!1;let o=(async()=>{let t=await n.get();n.lib=t,n.loaded=!0,a(n);let r=eZ(this.shareScopeMap,e,n,this.hooks.lifecycle.resolveShare);return r&&(r.lib=t,r.loaded=!0),t})();return this.setShared({pkgName:e,loaded:!1,shared:n,from:r.options.name,lib:null,loading:o}),o}}initializeSharing(){let e=arguments.length>0&&void 0!==arguments[0]?arguments[0]:q,t=arguments.length>1?arguments[1]:void 0,{host:r}=this,o=null==t?void 0:t.from,n=null==t?void 0:t.strategy,i=null==t?void 0:t.initScope,a=[];if("build"!==o){let{initTokens:t}=this;i||(i=[]);let r=t[e];if(r||(r=t[e]={from:this.host.name}),i.indexOf(r)>=0)return a;i.push(r)}let s=this.shareScopeMap,l=r.options.name;s[e]||(s[e]={});let c=s[e],d=(e,t)=>{var r;let{version:o,eager:n}=t;c[e]=c[e]||{};let i=c[e],a=i[o],s=!!(a&&(a.eager||(null==(r=a.shareConfig)?void 0:r.eager)));(!a||"loaded-first"!==a.strategy&&!a.loaded&&(!n!=!s?n:l>a.from))&&(i[o]=t)},u=t=>t&&t.init&&t.init(s[e],i),p=async e=>{let{module:t}=await r.remoteHandler.getRemoteModuleAndOptions({id:e});if(t.getEntry){let o;try{o=await t.getEntry()}catch(t){o=await r.remoteHandler.hooks.lifecycle.errorLoadRemote.emit({id:e,error:t,from:"runtime",lifecycle:"beforeLoadShare",origin:r})}t.inited||(await u(o),t.inited=!0)}};return Object.keys(r.options.shared).forEach(t=>{r.options.shared[t].forEach(r=>{r.scope.includes(e)&&d(t,r)})}),("version-first"===r.options.shareStrategy||"version-first"===n)&&r.options.remotes.forEach(t=>{t.shareScope===e&&a.push(p(t.name))}),a}loadShareSync(e,t){let{host:r}=this,o=e0({pkgName:e,extraOptions:t,shareInfos:r.options.shared});(null==o?void 0:o.scope)&&o.scope.forEach(e=>{this.initializeSharing(e,{strategy:o.strategy})});let n=eZ(this.shareScopeMap,e,o,this.hooks.lifecycle.resolveShare),a=e=>{e.useIn||(e.useIn=[]),u(e.useIn,r.options.name)};if(n){if("function"==typeof n.lib)return a(n),n.loaded||(n.loaded=!0,n.from===r.options.name&&(o.loaded=!0)),n.lib;if("function"==typeof n.get){let t=n.get();if(!(t instanceof Promise))return a(n),this.setShared({pkgName:e,loaded:!0,from:r.options.name,lib:t,shared:n}),t}}if(o.lib)return o.loaded||(o.loaded=!0),o.lib;if(o.get){let n=o.get();if(n instanceof Promise){let o=(null==t?void 0:t.from)==="build"?i.RUNTIME_005:i.RUNTIME_006;throw Error(i.getShortErrorMsg(o,i.runtimeDescMap,{hostName:r.options.name,sharedPkgName:e}))}return o.lib=n,this.setShared({pkgName:e,loaded:!0,from:r.options.name,lib:o.lib,shared:o}),o.lib}throw Error(i.getShortErrorMsg(i.RUNTIME_006,i.runtimeDescMap,{hostName:r.options.name,sharedPkgName:e}))}initShareScopeMap(e,t){let r=arguments.length>2&&void 0!==arguments[2]?arguments[2]:{},{host:o}=this;this.shareScopeMap[e]=t,this.hooks.lifecycle.initContainerShareScopeMap.emit({shareScope:t,options:o.options,origin:o,scopeName:e,hostShareScopeMap:r.hostShareScopeMap})}setShared(e){let{pkgName:t,shared:r,from:n,lib:i,loading:a,loaded:s,get:l}=e,{version:c,scope:d="default"}=r,u=o._object_without_properties_loose(r,["version","scope"]);(Array.isArray(d)?d:[d]).forEach(e=>{if(this.shareScopeMap[e]||(this.shareScopeMap[e]={}),this.shareScopeMap[e][t]||(this.shareScopeMap[e][t]={}),!this.shareScopeMap[e][t][c]){this.shareScopeMap[e][t][c]=o._extends({version:c,scope:["default"]},u,{lib:i,loaded:s,loading:a}),l&&(this.shareScopeMap[e][t][c].get=l);return}let r=this.shareScopeMap[e][t][c];a&&!r.loading&&(r.loading=a)})}_setGlobalShareScopeMap(e){let t=eQ(),r=e.id||e.name;r&&!t[r]&&(t[r]=this.shareScopeMap)}constructor(e){this.hooks=new td({afterResolve:new tc("afterResolve"),beforeLoadShare:new tc("beforeLoadShare"),loadShare:new ta,resolveShare:new tl("resolveShare"),initContainerShareScopeMap:new tl("initContainerShareScopeMap")}),this.host=e,this.shareScopeMap={},this.initTokens={},this._setGlobalShareScopeMap(e.options)}}class t${formatAndRegisterRemote(e,t){return(t.remotes||[]).reduce((e,t)=>(this.registerRemote(t,e,{force:!1}),e),e.remotes)}setIdToRemoteMap(e,t){let{remote:r,expose:o}=t,{name:n,alias:i}=r;if(this.idToRemoteMap[e]={name:r.name,expose:o},i&&e.startsWith(n)){let t=e.replace(n,i);this.idToRemoteMap[t]={name:r.name,expose:o};return}if(i&&e.startsWith(i)){let t=e.replace(i,n);this.idToRemoteMap[t]={name:r.name,expose:o}}}async loadRemote(e,t){let{host:r}=this;try{let{loadFactory:o=!0}=t||{loadFactory:!0},{module:n,moduleOptions:i,remoteMatchInfo:a}=await this.getRemoteModuleAndOptions({id:e}),{pkgNameOrAlias:s,remote:l,expose:c,id:d,remoteSnapshot:u}=a,p=await n.get(d,c,t,u),h=await this.hooks.lifecycle.onLoad.emit({id:d,pkgNameOrAlias:s,expose:c,exposeModule:o?p:void 0,exposeModuleFactory:o?void 0:p,remote:l,options:i,moduleInstance:n,origin:r});if(this.setIdToRemoteMap(e,a),"function"==typeof h)return h;return p}catch(i){let{from:o="runtime"}=t||{from:"runtime"},n=await this.hooks.lifecycle.errorLoadRemote.emit({id:e,error:i,from:o,lifecycle:"onLoad",origin:r});if(!n)throw i;return n}}async preloadRemote(e){let{host:t}=this;await this.hooks.lifecycle.beforePreloadRemote.emit({preloadOps:e,options:t.options,origin:t});let r=tp(t.options.remotes,e);await Promise.all(r.map(async e=>{let{remote:r}=e,o=to(r),{globalSnapshot:n,remoteSnapshot:i}=await t.snapshotHandler.loadRemoteSnapshotInfo(r),a=await this.hooks.lifecycle.generatePreloadAssets.emit({origin:t,preloadOptions:e,remote:r,remoteInfo:o,globalSnapshot:n,remoteSnapshot:i});a&&tf(o,t,a)}))}registerRemotes(e,t){let{host:r}=this;e.forEach(e=>{this.registerRemote(e,r.options.remotes,{force:null==t?void 0:t.force})})}async getRemoteModuleAndOptions(e){let t,{host:r}=this,{id:n}=e;try{t=await this.hooks.lifecycle.beforeRequest.emit({id:n,options:r.options,origin:r})}catch(e){if(!(t=await this.hooks.lifecycle.errorLoadRemote.emit({id:n,options:r.options,origin:r,from:"runtime",error:e,lifecycle:"beforeRequest"})))throw e}let{id:a}=t,s=e5(r.options.remotes,a);l(s,i.getShortErrorMsg(i.RUNTIME_004,i.runtimeDescMap,{hostName:r.options.name,requestId:a}));let{remote:c}=s,d=to(c),u=await r.sharedHandler.hooks.lifecycle.afterResolve.emit(o._extends({id:a},s,{options:r.options,origin:r,remoteInfo:d})),{remote:p,expose:h}=u;l(p&&h,`The 'beforeRequest' hook was executed, but it failed to return the correct 'remote' and 'expose' values while loading ${a}.`);let f=r.moduleCache.get(p.name),m={host:r,remoteInfo:d};return f||(f=new tn(m),r.moduleCache.set(p.name,f)),{module:f,moduleOptions:m,remoteMatchInfo:u}}registerRemote(e,t,r){let{host:o}=this,i=()=>{if(e.alias){let r=t.find(t=>{var r;return e.alias&&(t.name.startsWith(e.alias)||(null==(r=t.alias)?void 0:r.startsWith(e.alias)))});l(!r,`The alias ${e.alias} of remote ${e.name} is not allowed to be the prefix of ${r&&r.name} name or alias`)}"entry"in e&&n.isBrowserEnv()&&!e.entry.startsWith("http")&&(e.entry=new URL(e.entry,window.location.origin).href),e.shareScope||(e.shareScope=q),e.type||(e.type=z)};this.hooks.lifecycle.beforeRegisterRemote.emit({remote:e,origin:o});let a=t.find(t=>t.name===e.name);if(a){let s=[`The remote "${e.name}" is already registered.`,"Please note that overriding it may cause unexpected errors."];(null==r?void 0:r.force)&&(this.removeRemote(a),i(),t.push(e),this.hooks.lifecycle.registerRemote.emit({remote:e,origin:o}),n.warn(s.join(" ")))}else i(),t.push(e),this.hooks.lifecycle.registerRemote.emit({remote:e,origin:o})}removeRemote(e){try{let{host:r}=this,{name:o}=e,i=r.options.remotes.findIndex(e=>e.name===o);-1!==i&&r.options.remotes.splice(i,1);let a=r.moduleCache.get(e.name);if(a){let o=a.remoteInfo,i=o.entryGlobalName;if(x[i]){var t;(null==(t=Object.getOwnPropertyDescriptor(x,i))?void 0:t.configurable)?delete x[i]:x[i]=void 0}let s=tt(a.remoteInfo);T[s]&&delete T[s],r.snapshotHandler.manifestCache.delete(o.entry);let l=o.buildVersion?n.composeKeyWithSeparator(o.name,o.buildVersion):o.name,c=x.__FEDERATION__.__INSTANCES__.findIndex(e=>o.buildVersion?e.options.id===l:e.name===l);if(-1!==c){let e=x.__FEDERATION__.__INSTANCES__[c];l=e.options.id||l;let t=eQ(),r=!0,n=[];Object.keys(t).forEach(e=>{let i=t[e];i&&Object.keys(i).forEach(t=>{let a=i[t];a&&Object.keys(a).forEach(i=>{let s=a[i];s&&Object.keys(s).forEach(a=>{let l=s[a];l&&"object"==typeof l&&l.from===o.name&&(l.loaded||l.loading?(l.useIn=l.useIn.filter(e=>e!==o.name),l.useIn.length?r=!1:n.push([e,t,i,a])):n.push([e,t,i,a]))})})})}),r&&(e.shareScopeMap={},delete t[l]),n.forEach(e=>{var r,o,n;let[i,a,s,l]=e;null==(n=t[i])||null==(o=n[a])||null==(r=o[s])||delete r[l]}),x.__FEDERATION__.__INSTANCES__.splice(c,1)}let{hostGlobalSnapshot:d}=tS(e,r);if(d){let t=d&&"remotesInfo"in d&&d.remotesInfo&&P(d.remotesInfo,e.name).key;t&&(delete d.remotesInfo[t],$.__FEDERATION__.__MANIFEST_LOADING__[t]&&delete $.__FEDERATION__.__MANIFEST_LOADING__[t])}r.moduleCache.delete(e.name)}}catch(e){s.log("removeRemote fail: ",e)}}constructor(e){this.hooks=new td({beforeRegisterRemote:new tl("beforeRegisterRemote"),registerRemote:new tl("registerRemote"),beforeRequest:new tc("beforeRequest"),onLoad:new ta("onLoad"),handlePreloadModule:new ti("handlePreloadModule"),errorLoadRemote:new ta("errorLoadRemote"),beforePreloadRemote:new ta("beforePreloadRemote"),generatePreloadAssets:new ta("generatePreloadAssets"),afterPreloadRemote:new ta,loadEntry:new ta}),this.host=e,this.idToRemoteMap={}}}class tR{initOptions(e){this.registerPlugins(e.plugins);let t=this.formatOptions(this.options,e);return this.options=t,t}async loadShare(e,t){return this.sharedHandler.loadShare(e,t)}loadShareSync(e,t){return this.sharedHandler.loadShareSync(e,t)}initializeSharing(){let e=arguments.length>0&&void 0!==arguments[0]?arguments[0]:q,t=arguments.length>1?arguments[1]:void 0;return this.sharedHandler.initializeSharing(e,t)}initRawContainer(e,t,r){let o=new tn({host:this,remoteInfo:to({name:e,entry:t})});return o.remoteEntryExports=r,this.moduleCache.set(e,o),o}async loadRemote(e,t){return this.remoteHandler.loadRemote(e,t)}async preloadRemote(e){return this.remoteHandler.preloadRemote(e)}initShareScopeMap(e,t){let r=arguments.length>2&&void 0!==arguments[2]?arguments[2]:{};this.sharedHandler.initShareScopeMap(e,t,r)}formatOptions(e,t){let{shared:r}=eG(e,t),{userOptions:n,options:i}=this.hooks.lifecycle.beforeInit.emit({origin:this,userOptions:t,options:e,shareInfo:r}),a=this.remoteHandler.formatAndRegisterRemote(i,n),{shared:s}=this.sharedHandler.registerShared(i,n),l=[...i.plugins];n.plugins&&n.plugins.forEach(e=>{l.includes(e)||l.push(e)});let c=o._extends({},e,t,{plugins:l,remotes:a,shared:s});return this.hooks.lifecycle.init.emit({origin:this,options:c}),c}registerPlugins(e){let t=e8(e,[this.hooks,this.remoteHandler.hooks,this.sharedHandler.hooks,this.snapshotHandler.hooks,this.loaderHook,this.bridgeHook]);this.options.plugins=this.options.plugins.reduce((e,t)=>(t&&e&&!e.find(e=>e.name===t.name)&&e.push(t),e),t||[])}registerRemotes(e,t){return this.remoteHandler.registerRemotes(e,t)}constructor(e){this.hooks=new td({beforeInit:new tl("beforeInit"),init:new ti,beforeInitContainer:new tc("beforeInitContainer"),initContainer:new tc("initContainer")}),this.version="0.13.1",this.moduleCache=new Map,this.loaderHook=new td({getModuleInfo:new ti,createScript:new ti,createLink:new ti,fetch:new ta,loadEntryError:new ta,getModuleFactory:new ta}),this.bridgeHook=new td({beforeBridgeRender:new ti,afterBridgeRender:new ti,beforeBridgeDestroy:new ti,afterBridgeDestroy:new ti});const t={id:e2(),name:e.name,plugins:[tg(),tv()],remotes:[],shared:{},inBrowser:n.isBrowserEnv()};this.name=e.name,this.options=t,this.snapshotHandler=new tx(this),this.sharedHandler=new tw(this),this.remoteHandler=new t$(this),this.shareScopeMap=this.sharedHandler.shareScopeMap,this.registerPlugins([...t.plugins,...e.plugins||[]]),this.options=this.formatOptions(t,e)}}var tI=Object.freeze({__proto__:null});t.loadScript=n.loadScript,t.loadScriptNode=n.loadScriptNode,t.CurrentGlobal=x,t.FederationHost=tR,t.Global=$,t.Module=tn,t.addGlobalSnapshot=H,t.assert=l,t.getGlobalFederationConstructor=A,t.getGlobalSnapshot=C,t.getInfoWithoutType=P,t.getRegisteredShare=eZ,t.getRemoteEntry=tr,t.getRemoteInfo=to,t.helpers=e1,t.isStaticResourcesEqual=_,t.matchRemoteWithNameAndExpose=e5,t.registerGlobalPlugins=U,t.resetFederationGlobalInfo=O,t.safeWrapper=m,t.satisfy=eB,t.setGlobalFederationConstructor=M,t.setGlobalFederationInstance=k,t.types=tI},9844:function(e,t){function r(){return(r=Object.assign||function(e){for(var t=1;t<arguments.length;t++){var r=arguments[t];for(var o in r)Object.prototype.hasOwnProperty.call(r,o)&&(e[o]=r[o])}return e}).apply(this,arguments)}function o(e,t){if(null==e)return{};var r,o,n={},i=Object.keys(e);for(o=0;o<i.length;o++)r=i[o],t.indexOf(r)>=0||(n[r]=e[r]);return n}t._extends=r,t._object_without_properties_loose=o},4124:function(e,t,r){var o=r(5976),n=r(3039);let i=null;function a(e){let t=n.getGlobalFederationInstance(e.name,e.version);return t?(t.initOptions(e),i||(i=t),t):(i=new(o.getGlobalFederationConstructor()||o.FederationHost)(e),o.setGlobalFederationInstance(i),i)}function s(){for(var e=arguments.length,t=Array(e),r=0;r<e;r++)t[r]=arguments[r];return o.assert(i,"Please call init first"),i.loadRemote.apply(i,t)}function l(){for(var e=arguments.length,t=Array(e),r=0;r<e;r++)t[r]=arguments[r];return o.assert(i,"Please call init first"),i.loadShare.apply(i,t)}function c(){for(var e=arguments.length,t=Array(e),r=0;r<e;r++)t[r]=arguments[r];return o.assert(i,"Please call init first"),i.loadShareSync.apply(i,t)}function d(){for(var e=arguments.length,t=Array(e),r=0;r<e;r++)t[r]=arguments[r];return o.assert(i,"Please call init first"),i.preloadRemote.apply(i,t)}function u(){for(var e=arguments.length,t=Array(e),r=0;r<e;r++)t[r]=arguments[r];return o.assert(i,"Please call init first"),i.registerRemotes.apply(i,t)}function p(){for(var e=arguments.length,t=Array(e),r=0;r<e;r++)t[r]=arguments[r];return o.assert(i,"Please call init first"),i.registerPlugins.apply(i,t)}function h(){return i}o.setGlobalFederationConstructor(o.FederationHost),t.FederationHost=o.FederationHost,t.Module=o.Module,t.getRemoteEntry=o.getRemoteEntry,t.getRemoteInfo=o.getRemoteInfo,t.loadScript=o.loadScript,t.loadScriptNode=o.loadScriptNode,t.registerGlobalPlugins=o.registerGlobalPlugins,t.getInstance=h,t.init=a,t.loadRemote=s,t.loadShare=l,t.loadShareSync=c,t.preloadRemote=d,t.registerPlugins=p,t.registerRemotes=u},3039:function(e,t,r){var o=r(5976);function n(){return"coreshoporder:1.0.0"}t.getGlobalFederationInstance=function(e,t){let r=n();return o.CurrentGlobal.__FEDERATION__.__INSTANCES__.find(o=>!!r&&o.options.id===n()||o.options.name===e&&!o.options.version&&!t||o.options.name===e&&!!t&&o.options.version===t)}},6012:function(__unused_webpack_module,exports,__webpack_require__){var polyfills=__webpack_require__(3400);let FederationModuleManifest="federation-manifest.json",MANIFEST_EXT=".json",BROWSER_LOG_KEY="FEDERATION_DEBUG",BROWSER_LOG_VALUE="1",NameTransformSymbol={AT:"@",HYPHEN:"-",SLASH:"/"},NameTransformMap={[NameTransformSymbol.AT]:"scope_",[NameTransformSymbol.HYPHEN]:"_",[NameTransformSymbol.SLASH]:"__"},EncodedNameTransformMap={[NameTransformMap[NameTransformSymbol.AT]]:NameTransformSymbol.AT,[NameTransformMap[NameTransformSymbol.HYPHEN]]:NameTransformSymbol.HYPHEN,[NameTransformMap[NameTransformSymbol.SLASH]]:NameTransformSymbol.SLASH},SEPARATOR=":",ManifestFileName="mf-manifest.json",StatsFileName="mf-stats.json",MFModuleType={NPM:"npm",APP:"app"},MODULE_DEVTOOL_IDENTIFIER="__MF_DEVTOOLS_MODULE_INFO__",ENCODE_NAME_PREFIX="ENCODE_NAME_PREFIX",TEMP_DIR=".federation",MFPrefetchCommon={identifier:"MFDataPrefetch",globalKey:"__PREFETCH__",library:"mf-data-prefetch",exportsKey:"__PREFETCH_EXPORTS__",fileName:"bootstrap.js"};var ContainerPlugin=Object.freeze({__proto__:null}),ContainerReferencePlugin=Object.freeze({__proto__:null}),ModuleFederationPlugin=Object.freeze({__proto__:null}),SharePlugin=Object.freeze({__proto__:null});function isBrowserEnv(){return"undefined"!=typeof window&&void 0!==window.document}function isReactNativeEnv(){var e;return"undefined"!=typeof navigator&&(null==(e=navigator)?void 0:e.product)==="ReactNative"}function isBrowserDebug(){try{if(isBrowserEnv()&&window.localStorage)return localStorage.getItem(BROWSER_LOG_KEY)===BROWSER_LOG_VALUE}catch(e){}return!1}function isDebugMode(){return"undefined"!=typeof process&&process.env&&process.env.FEDERATION_DEBUG?!!process.env.FEDERATION_DEBUG:!!("undefined"!=typeof FEDERATION_DEBUG&&FEDERATION_DEBUG)||isBrowserDebug()}let getProcessEnv=function(){return"undefined"!=typeof process&&process.env?process.env:{}},LOG_CATEGORY="[ Federation Runtime ]",parseEntry=function(e,t){let r=arguments.length>2&&void 0!==arguments[2]?arguments[2]:SEPARATOR,o=e.split(r),n="development"===getProcessEnv().NODE_ENV&&t,i="*",a=e=>e.startsWith("http")||e.includes(MANIFEST_EXT);if(o.length>=2){let[t,...s]=o;e.startsWith(r)&&(t=o.slice(0,2).join(r),s=[n||o.slice(2).join(r)]);let l=n||s.join(r);return a(l)?{name:t,entry:l}:{name:t,version:l||i}}if(1===o.length){let[e]=o;return n&&a(n)?{name:e,entry:n}:{name:e,version:n||i}}throw`Invalid entry value: ${e}`},composeKeyWithSeparator=function(){for(var e=arguments.length,t=Array(e),r=0;r<e;r++)t[r]=arguments[r];return t.length?t.reduce((e,t)=>t?e?`${e}${SEPARATOR}${t}`:t:e,""):""},encodeName=function(e){let t=arguments.length>1&&void 0!==arguments[1]?arguments[1]:"",r=arguments.length>2&&void 0!==arguments[2]&&arguments[2];try{let o=r?".js":"";return`${t}${e.replace(RegExp(`${NameTransformSymbol.AT}`,"g"),NameTransformMap[NameTransformSymbol.AT]).replace(RegExp(`${NameTransformSymbol.HYPHEN}`,"g"),NameTransformMap[NameTransformSymbol.HYPHEN]).replace(RegExp(`${NameTransformSymbol.SLASH}`,"g"),NameTransformMap[NameTransformSymbol.SLASH])}${o}`}catch(e){throw e}},decodeName=function(e,t,r){try{let o=e;if(t){if(!o.startsWith(t))return o;o=o.replace(RegExp(t,"g"),"")}return o=o.replace(RegExp(`${NameTransformMap[NameTransformSymbol.AT]}`,"g"),EncodedNameTransformMap[NameTransformMap[NameTransformSymbol.AT]]).replace(RegExp(`${NameTransformMap[NameTransformSymbol.SLASH]}`,"g"),EncodedNameTransformMap[NameTransformMap[NameTransformSymbol.SLASH]]).replace(RegExp(`${NameTransformMap[NameTransformSymbol.HYPHEN]}`,"g"),EncodedNameTransformMap[NameTransformMap[NameTransformSymbol.HYPHEN]]),r&&(o=o.replace(".js","")),o}catch(e){throw e}},generateExposeFilename=(e,t)=>{if(!e)return"";let r=e;return"."===r&&(r="default_export"),r.startsWith("./")&&(r=r.replace("./","")),encodeName(r,"__federation_expose_",t)},generateShareFilename=(e,t)=>e?encodeName(e,"__federation_shared_",t):"",getResourceUrl=(e,t)=>{if("getPublicPath"in e){let r;return r=e.getPublicPath.startsWith("function")?Function("return "+e.getPublicPath)()():Function(e.getPublicPath)(),`${r}${t}`}return"publicPath"in e?!isBrowserEnv()&&!isReactNativeEnv()&&"ssrPublicPath"in e?`${e.ssrPublicPath}${t}`:`${e.publicPath}${t}`:(console.warn("Cannot get resource URL. If in debug mode, please ignore.",e,t),"")},assert=(e,t)=>{e||error(t)},error=e=>{throw Error(`${LOG_CATEGORY}: ${e}`)},warn=e=>{console.warn(`${LOG_CATEGORY}: ${e}`)};function safeToString(e){try{return JSON.stringify(e,null,2)}catch(e){return""}}let VERSION_PATTERN_REGEXP=/^([\d^=v<>~]|[*xX]$)/;function isRequiredVersion(e){return VERSION_PATTERN_REGEXP.test(e)}let simpleJoinRemoteEntry=(e,t)=>{if(!e)return t;let r=(e=>{if("."===e)return"";if(e.startsWith("./"))return e.replace("./","");if(e.startsWith("/")){let t=e.slice(1);return t.endsWith("/")?t.slice(0,-1):t}return e})(e);return r?r.endsWith("/")?`${r}${t}`:`${r}/${t}`:t};function inferAutoPublicPath(e){return e.replace(/#.*$/,"").replace(/\?.*$/,"").replace(/\/[^\/]+$/,"/")}function generateSnapshotFromManifest(e){var t,r,o;let n,i=arguments.length>1&&void 0!==arguments[1]?arguments[1]:{},{remotes:a={},overrides:s={},version:l}=i,c=()=>"publicPath"in e.metaData?"auto"===e.metaData.publicPath&&l?inferAutoPublicPath(l):e.metaData.publicPath:e.metaData.getPublicPath,d=Object.keys(s),u={};Object.keys(a).length||(u=(null==(o=e.remotes)?void 0:o.reduce((e,t)=>{let r,o=t.federationContainerName;return r=d.includes(o)?s[o]:"version"in t?t.version:t.entry,e[o]={matchedVersion:r},e},{}))||{}),Object.keys(a).forEach(e=>u[e]={matchedVersion:d.includes(e)?s[e]:a[e]});let{remoteEntry:{path:p,name:h,type:f},types:m,buildInfo:{buildVersion:g},globalName:y,ssrRemoteEntry:b}=e.metaData,{exposes:_}=e,E={version:l||"",buildVersion:g,globalName:y,remoteEntry:simpleJoinRemoteEntry(p,h),remoteEntryType:f,remoteTypes:simpleJoinRemoteEntry(m.path,m.name),remoteTypesZip:m.zip||"",remoteTypesAPI:m.api||"",remotesInfo:u,shared:null==e?void 0:e.shared.map(e=>({assets:e.assets,sharedName:e.name,version:e.version})),modules:null==_?void 0:_.map(e=>({moduleName:e.name,modulePath:e.path,assets:e.assets}))};if(null==(t=e.metaData)?void 0:t.prefetchInterface){let t=e.metaData.prefetchInterface;E=polyfills._({},E,{prefetchInterface:t})}if(null==(r=e.metaData)?void 0:r.prefetchEntry){let{path:t,name:r,type:o}=e.metaData.prefetchEntry;E=polyfills._({},E,{prefetchEntry:simpleJoinRemoteEntry(t,r),prefetchEntryType:o})}return n="publicPath"in e.metaData?polyfills._({},E,{publicPath:c(),ssrPublicPath:e.metaData.ssrPublicPath}):polyfills._({},E,{getPublicPath:c()}),b&&(n.ssrRemoteEntry=simpleJoinRemoteEntry(b.path,b.name),n.ssrRemoteEntryType=b.type||"commonjs-module"),n}function isManifestProvider(e){return!!("remoteEntry"in e&&e.remoteEntry.includes(MANIFEST_EXT))}let PREFIX="[ Module Federation ]",Logger=class{setPrefix(e){this.prefix=e}log(){for(var e=arguments.length,t=Array(e),r=0;r<e;r++)t[r]=arguments[r];console.log(this.prefix,...t)}warn(){for(var e=arguments.length,t=Array(e),r=0;r<e;r++)t[r]=arguments[r];console.log(this.prefix,...t)}error(){for(var e=arguments.length,t=Array(e),r=0;r<e;r++)t[r]=arguments[r];console.log(this.prefix,...t)}success(){for(var e=arguments.length,t=Array(e),r=0;r<e;r++)t[r]=arguments[r];console.log(this.prefix,...t)}info(){for(var e=arguments.length,t=Array(e),r=0;r<e;r++)t[r]=arguments[r];console.log(this.prefix,...t)}ready(){for(var e=arguments.length,t=Array(e),r=0;r<e;r++)t[r]=arguments[r];console.log(this.prefix,...t)}debug(){for(var e=arguments.length,t=Array(e),r=0;r<e;r++)t[r]=arguments[r];isDebugMode()&&console.log(this.prefix,...t)}constructor(e){this.prefix=e}};function createLogger(e){return new Logger(e)}let logger=createLogger(PREFIX);async function safeWrapper(e,t){try{return await e()}catch(e){t||warn(e);return}}function isStaticResourcesEqual(e,t){let r=/^(https?:)?\/\//i;return e.replace(r,"").replace(/\/$/,"")===t.replace(r,"").replace(/\/$/,"")}function createScript(e){let t,r=null,o=!0,n=2e4,i=document.getElementsByTagName("script");for(let t=0;t<i.length;t++){let n=i[t],a=n.getAttribute("src");if(a&&isStaticResourcesEqual(a,e.url)){r=n,o=!1;break}}if(!r){let t,o=e.attrs;(r=document.createElement("script")).type=(null==o?void 0:o.type)==="module"?"module":"text/javascript",e.createScriptHook&&((t=e.createScriptHook(e.url,e.attrs))instanceof HTMLScriptElement?r=t:"object"==typeof t&&("script"in t&&t.script&&(r=t.script),"timeout"in t&&t.timeout&&(n=t.timeout))),r.src||(r.src=e.url),o&&!t&&Object.keys(o).forEach(e=>{r&&("async"===e||"defer"===e?r[e]=o[e]:r.getAttribute(e)||r.setAttribute(e,o[e]))})}let a=async(o,n)=>{clearTimeout(t);let i=()=>{(null==n?void 0:n.type)==="error"?(null==e?void 0:e.onErrorCallback)&&(null==e||e.onErrorCallback(n)):(null==e?void 0:e.cb)&&(null==e||e.cb())};if(r&&(r.onerror=null,r.onload=null,safeWrapper(()=>{let{needDeleteScript:t=!0}=e;t&&(null==r?void 0:r.parentNode)&&r.parentNode.removeChild(r)}),o&&"function"==typeof o)){let e=o(n);if(e instanceof Promise){let t=await e;return i(),t}return i(),e}i()};return r.onerror=a.bind(null,r.onerror),r.onload=a.bind(null,r.onload),t=setTimeout(()=>{a(null,Error(`Remote script "${e.url}" time-outed.`))},n),{script:r,needAttach:o}}function createLink(e){let t=null,r=!0,o=document.getElementsByTagName("link");for(let n=0;n<o.length;n++){let i=o[n],a=i.getAttribute("href"),s=i.getAttribute("rel");if(a&&isStaticResourcesEqual(a,e.url)&&s===e.attrs.rel){t=i,r=!1;break}}if(!t){let r;(t=document.createElement("link")).setAttribute("href",e.url);let o=e.attrs;e.createLinkHook&&(r=e.createLinkHook(e.url,o))instanceof HTMLLinkElement&&(t=r),o&&!r&&Object.keys(o).forEach(e=>{t&&!t.getAttribute(e)&&t.setAttribute(e,o[e])})}let n=(r,o)=>{let n=()=>{(null==o?void 0:o.type)==="error"?(null==e?void 0:e.onErrorCallback)&&(null==e||e.onErrorCallback(o)):(null==e?void 0:e.cb)&&(null==e||e.cb())};if(t&&(t.onerror=null,t.onload=null,safeWrapper(()=>{let{needDeleteLink:r=!0}=e;r&&(null==t?void 0:t.parentNode)&&t.parentNode.removeChild(t)}),r)){let e=r(o);return n(),e}n()};return t.onerror=n.bind(null,t.onerror),t.onload=n.bind(null,t.onload),{link:t,needAttach:r}}function loadScript(e,t){let{attrs:r={},createScriptHook:o}=t;return new Promise((t,n)=>{let{script:i,needAttach:a}=createScript({url:e,cb:t,onErrorCallback:n,attrs:polyfills._({fetchpriority:"high"},r),createScriptHook:o,needDeleteScript:!0});a&&document.head.appendChild(i)})}function importNodeModule(e){if(!e)throw Error("import specifier is required");return Function("name","return import(name)")(e).then(e=>e).catch(t=>{throw console.error(`Error importing module ${e}:`,t),t})}let loadNodeFetch=async()=>{let e=await importNodeModule("node-fetch");return e.default||e},lazyLoaderHookFetch=async(e,t,r)=>{let o=(e,t)=>r.lifecycle.fetch.emit(e,t),n=await o(e,t||{});return n&&n instanceof Response?n:("undefined"==typeof fetch?await loadNodeFetch():fetch)(e,t||{})};function createScriptNode(url,cb,attrs,loaderHook){let urlObj;if(null==loaderHook?void 0:loaderHook.createScriptHook){let hookResult=loaderHook.createScriptHook(url);hookResult&&"object"==typeof hookResult&&"url"in hookResult&&(url=hookResult.url)}try{urlObj=new URL(url)}catch(e){console.error("Error constructing URL:",e),cb(Error(`Invalid URL: ${e}`));return}let getFetch=async()=>(null==loaderHook?void 0:loaderHook.fetch)?(e,t)=>lazyLoaderHookFetch(e,t,loaderHook):"undefined"==typeof fetch?loadNodeFetch():fetch,handleScriptFetch=async(f,urlObj)=>{try{var _vm_constants,_vm_constants_USE_MAIN_CONTEXT_DEFAULT_LOADER;let res=await f(urlObj.href),data=await res.text(),[path,vm]=await Promise.all([importNodeModule("path"),importNodeModule("vm")]),scriptContext={exports:{},module:{exports:{}}},urlDirname=urlObj.pathname.split("/").slice(0,-1).join("/"),filename=path.basename(urlObj.pathname),script=new vm.Script(`(function(exports, module, require, __dirname, __filename) {${data}
})`,{filename,importModuleDynamically:null!=(_vm_constants_USE_MAIN_CONTEXT_DEFAULT_LOADER=null==(_vm_constants=vm.constants)?void 0:_vm_constants.USE_MAIN_CONTEXT_DEFAULT_LOADER)?_vm_constants_USE_MAIN_CONTEXT_DEFAULT_LOADER:importNodeModule});script.runInThisContext()(scriptContext.exports,scriptContext.module,eval("require"),urlDirname,filename);let exportedInterface=scriptContext.module.exports||scriptContext.exports;if(attrs&&exportedInterface&&attrs.globalName){let container=exportedInterface[attrs.globalName]||exportedInterface;cb(void 0,container);return}cb(void 0,exportedInterface)}catch(e){cb(e instanceof Error?e:Error(`Script execution error: ${e}`))}};getFetch().then(async e=>{if((null==attrs?void 0:attrs.type)==="esm"||(null==attrs?void 0:attrs.type)==="module")return loadModule(urlObj.href,{fetch:e,vm:await importNodeModule("vm")}).then(async e=>{await e.evaluate(),cb(void 0,e.namespace)}).catch(e=>{cb(e instanceof Error?e:Error(`Script execution error: ${e}`))});handleScriptFetch(e,urlObj)}).catch(e=>{cb(e)})}function loadScriptNode(e,t){return new Promise((r,o)=>{createScriptNode(e,(e,n)=>{if(e)o(e);else{var i,a;let e=(null==t||null==(i=t.attrs)?void 0:i.globalName)||`__FEDERATION_${null==t||null==(a=t.attrs)?void 0:a.name}:custom__`;r(globalThis[e]=n)}},t.attrs,t.loaderHook)})}async function loadModule(e,t){let{fetch:r,vm:o}=t,n=await r(e),i=await n.text(),a=new o.SourceTextModule(i,{importModuleDynamically:async(r,o)=>loadModule(new URL(r,e).href,t)});return await a.link(async r=>{let o=new URL(r,e).href;return await loadModule(o,t)}),a}function normalizeOptions(e,t,r){return function(o){if(!1===o)return!1;if(void 0===o)if(e)return t;else return!1;if(!0===o)return t;if(o&&"object"==typeof o)return polyfills._({},t,o);throw Error(`Unexpected type for \`${r}\`, expect boolean/undefined/object, got: ${typeof o}`)}}exports.BROWSER_LOG_KEY=BROWSER_LOG_KEY,exports.BROWSER_LOG_VALUE=BROWSER_LOG_VALUE,exports.ENCODE_NAME_PREFIX=ENCODE_NAME_PREFIX,exports.EncodedNameTransformMap=EncodedNameTransformMap,exports.FederationModuleManifest=FederationModuleManifest,exports.MANIFEST_EXT=MANIFEST_EXT,exports.MFModuleType=MFModuleType,exports.MFPrefetchCommon=MFPrefetchCommon,exports.MODULE_DEVTOOL_IDENTIFIER=MODULE_DEVTOOL_IDENTIFIER,exports.ManifestFileName=ManifestFileName,exports.NameTransformMap=NameTransformMap,exports.NameTransformSymbol=NameTransformSymbol,exports.SEPARATOR=SEPARATOR,exports.StatsFileName=StatsFileName,exports.TEMP_DIR=TEMP_DIR,exports.assert=assert,exports.composeKeyWithSeparator=composeKeyWithSeparator,exports.containerPlugin=ContainerPlugin,exports.containerReferencePlugin=ContainerReferencePlugin,exports.createLink=createLink,exports.createLogger=createLogger,exports.createScript=createScript,exports.createScriptNode=createScriptNode,exports.decodeName=decodeName,exports.encodeName=encodeName,exports.error=error,exports.generateExposeFilename=generateExposeFilename,exports.generateShareFilename=generateShareFilename,exports.generateSnapshotFromManifest=generateSnapshotFromManifest,exports.getProcessEnv=getProcessEnv,exports.getResourceUrl=getResourceUrl,exports.inferAutoPublicPath=inferAutoPublicPath,exports.isBrowserEnv=isBrowserEnv,exports.isDebugMode=isDebugMode,exports.isManifestProvider=isManifestProvider,exports.isReactNativeEnv=isReactNativeEnv,exports.isRequiredVersion=isRequiredVersion,exports.isStaticResourcesEqual=isStaticResourcesEqual,exports.loadScript=loadScript,exports.loadScriptNode=loadScriptNode,exports.logger=logger,exports.moduleFederationPlugin=ModuleFederationPlugin,exports.normalizeOptions=normalizeOptions,exports.parseEntry=parseEntry,exports.safeToString=safeToString,exports.safeWrapper=safeWrapper,exports.sharePlugin=SharePlugin,exports.simpleJoinRemoteEntry=simpleJoinRemoteEntry,exports.warn=warn},3400:function(e,t){function r(){return(r=Object.assign||function(e){for(var t=1;t<arguments.length;t++){var r=arguments[t];for(var o in r)Object.prototype.hasOwnProperty.call(r,o)&&(e[o]=r[o])}return e}).apply(this,arguments)}t._=r},7159:function(e,t){t.FEDERATION_SUPPORTED_TYPES=["script"]},7945:function(e,t,r){var o=r(4124),n=r(7159),i=r(6012);function a(e){e.S&&!e.federation.hasAttachShareScopeMap&&e.federation.instance&&e.federation.instance.shareScopeMap&&(e.S=e.federation.instance.shareScopeMap,e.federation.hasAttachShareScopeMap=!0)}function s(e){let{chunkId:t,promises:r,chunkMapping:o,idToExternalAndNameMapping:s,webpackRequire:l,idToRemoteMap:c}=e;a(l),l.o(o,t)&&o[t].forEach(e=>{let t=l.R;t||(t=[]);let o=s[e],a=c[e];if(t.indexOf(o)>=0)return;if(t.push(o),o.p)return r.push(o.p);let d=t=>{t||(t=Error("Container missing")),"string"==typeof t.message&&(t.message+=`
while loading "${o[1]}" from ${o[2]}`),l.m[e]=()=>{throw t},o.p=0},u=(e,t,n,i,a,s)=>{try{let l=e(t,n);if(!l||!l.then)return a(l,i,s);{let e=l.then(e=>a(e,i),d);if(!s)return e;r.push(o.p=e)}}catch(e){d(e)}},p=(e,t,r)=>e?u(l.I,o[0],0,e,h,r):d();var h=(e,r,n)=>u(r.get,o[1],t,0,f,n),f=t=>{o.p=1,l.m[e]=e=>{e.exports=t()}};let m=()=>{try{let e=i.decodeName(a[0].name,i.ENCODE_NAME_PREFIX)+o[1].slice(1),t=l.federation.instance,r=()=>l.federation.instance.loadRemote(e,{loadFactory:!1,from:"build"});if("version-first"===t.options.shareStrategy)return Promise.all(t.sharedHandler.initializeSharing(o[0])).then(()=>r());return r()}catch(e){d(e)}};1===a.length&&n.FEDERATION_SUPPORTED_TYPES.includes(a[0].externalType)&&a[0].name?u(m,o[2],0,0,f,1):u(l,o[2],0,0,p,1)})}function l(e){let{chunkId:t,promises:r,chunkMapping:o,installedModules:n,moduleToHandlerMapping:i,webpackRequire:s}=e;a(s),s.o(o,t)&&o[t].forEach(e=>{if(s.o(n,e))return r.push(n[e]);let t=t=>{n[e]=0,s.m[e]=r=>{delete s.c[e],r.exports=t()}},o=t=>{delete n[e],s.m[e]=r=>{throw delete s.c[e],t}};try{let a=s.federation.instance;if(!a)throw Error("Federation instance not found!");let{shareKey:l,getter:c,shareInfo:d}=i[e],u=a.loadShare(l,{customShareInfo:d}).then(e=>!1===e?c():e);u.then?r.push(n[e]=u.then(t).catch(o)):t(u)}catch(e){o(e)}})}function c(e){let{shareScopeName:t,webpackRequire:r,initPromises:o,initTokens:i,initScope:s}=e,l=Array.isArray(t)?t:[t];var c=[],d=function(e){s||(s=[]);let l=r.federation.instance;var c=i[e];if(c||(c=i[e]={from:l.name}),s.indexOf(c)>=0)return;s.push(c);let d=o[e];if(d)return d;var u=e=>"undefined"!=typeof console&&console.warn&&console.warn(e),p=o=>{var n=e=>u("Initialization of sharing external failed: "+e);try{var i=r(o);if(!i)return;var a=o=>o&&o.init&&o.init(r.S[e],s,{shareScopeMap:r.S||{},shareScopeKeys:t});if(i.then)return h.push(i.then(a,n));var l=a(i);if(l&&"boolean"!=typeof l&&l.then)return h.push(l.catch(n))}catch(e){n(e)}};let h=l.initializeSharing(e,{strategy:l.options.shareStrategy,initScope:s,from:"build"});a(r);let f=r.federation.bundlerRuntimeOptions.remotes;return(f&&Object.keys(f.idToRemoteMap).forEach(e=>{let t=f.idToRemoteMap[e],r=f.idToExternalAndNameMapping[e][2];if(t.length>1)p(r);else if(1===t.length){let e=t[0];n.FEDERATION_SUPPORTED_TYPES.includes(e.externalType)||p(r)}}),h.length)?o[e]=Promise.all(h).then(()=>o[e]=!0):o[e]=!0};return l.forEach(e=>{c.push(d(e))}),Promise.all(c).then(()=>!0)}function d(e){let{moduleId:t,moduleToHandlerMapping:r,webpackRequire:o}=e,n=o.federation.instance;if(!n)throw Error("Federation instance not found!");let{shareKey:i,shareInfo:a}=r[t];try{return n.loadShareSync(i,{customShareInfo:a})}catch(e){throw console.error('loadShareSync failed! The function should not be called unless you set "eager:true". If you do not set it, and encounter this issue, you can check whether an async boundary is implemented.'),console.error("The original error message is as follows: "),e}}function u(e){let{moduleToHandlerMapping:t,webpackRequire:r,installedModules:o,initialConsumes:n}=e;n.forEach(e=>{r.m[e]=n=>{o[e]=0,delete r.c[e];let i=d({moduleId:e,moduleToHandlerMapping:t,webpackRequire:r});if("function"!=typeof i)throw Error(`Shared module is not available for eager consumption: ${e}`);n.exports=i()}})}function p(){return(p=Object.assign||function(e){for(var t=1;t<arguments.length;t++){var r=arguments[t];for(var o in r)Object.prototype.hasOwnProperty.call(r,o)&&(e[o]=r[o])}return e}).apply(this,arguments)}function h(e){let{webpackRequire:t,shareScope:r,initScope:o,shareScopeKey:n,remoteEntryInitOptions:i}=e;if(!t.S||!t.federation||!t.federation.instance||!t.federation.initOptions)return;let a=t.federation.instance;a.initOptions(p({name:t.federation.initOptions.name,remotes:[]},i));let s=null==i?void 0:i.shareScopeKeys,l=null==i?void 0:i.shareScopeMap;if(n&&"string"!=typeof n)n.forEach(e=>{if(!s||!l)return void a.initShareScopeMap(e,r,{hostShareScopeMap:(null==i?void 0:i.shareScopeMap)||{}});l[e]||(l[e]={});let t=l[e];a.initShareScopeMap(e,t,{hostShareScopeMap:(null==i?void 0:i.shareScopeMap)||{}})});else{let e=n||"default";Array.isArray(s)?s.forEach(e=>{l[e]||(l[e]={});let t=l[e];a.initShareScopeMap(e,t,{hostShareScopeMap:(null==i?void 0:i.shareScopeMap)||{}})}):a.initShareScopeMap(e,r,{hostShareScopeMap:(null==i?void 0:i.shareScopeMap)||{}})}return(t.federation.attachShareScopeMap&&t.federation.attachShareScopeMap(t),"function"==typeof t.federation.prefetch&&t.federation.prefetch(),Array.isArray(n))?t.federation.initOptions.shared?t.I(n,o):Promise.all(n.map(e=>t.I(e,o))).then(()=>!0):t.I(n||"default",o)}e.exports={runtime:function(e){var t=Object.create(null);if(e)for(var r in e)t[r]=e[r];return t.default=e,Object.freeze(t)}(o),instance:void 0,initOptions:void 0,bundlerRuntime:{remotes:s,consumes:l,I:c,S:{},installInitialConsumes:u,initContainerEntry:h},attachShareScopeMap:a,bundlerRuntimeOptions:{}}},8920:function(e,t,r){r(2977),r(4781),r(5572);var o=r(2855),n=r(5168),i=r.n(n),a=r(8701),s=r(3842),l=r(3374);class c extends l.G${async getConfig(){let e=this.cfg,t=`${e.basePath}${e.resourcePath}/get-config`,r=await fetch(t,{method:"GET",headers:{"Content-Type":"application/json"},credentials:"same-origin"});if(!r.ok)throw Error(`Failed to get config: ${r.statusText}`);return r.json()}}r(2696),r(3933),r(5210);class d extends c{async getVoucherCodes(e,t){let r=this.cfg,o=new URLSearchParams;o.append("cartPriceRule",e.toString()),(null==t?void 0:t.start)!==void 0&&o.append("start",t.start.toString()),(null==t?void 0:t.limit)!==void 0&&o.append("limit",t.limit.toString());let n=`${r.basePath}${r.resourcePath}/get-voucher-codes?${o}`,i=await fetch(n,{method:"GET",headers:{"Content-Type":"application/json"},credentials:"same-origin"});if(!i.ok)throw Error(`Failed to get voucher codes: ${i.statusText}`);return i.json()}async createVoucherCode(e,t){let r=this.cfg,o=`${r.basePath}${r.resourcePath}/create-voucher-code`,n=await fetch(o,{method:"POST",headers:{"Content-Type":"application/json"},credentials:"same-origin",body:JSON.stringify({cartPriceRule:e,code:t})});if(!n.ok)throw Error((await n.json()).message||"Failed to create voucher code");let i=await n.json();if(!i.success)throw Error(i.message||"Failed to create voucher code");return i.data}async generateVoucherCodes(e){let t=this.cfg,r=`${t.basePath}${t.resourcePath}/generate-voucher-codes`,o=await fetch(r,{method:"POST",headers:{"Content-Type":"application/json"},credentials:"same-origin",body:JSON.stringify(e)});if(!o.ok)throw Error((await o.json()).message||"Failed to generate voucher codes");let n=await o.json();if(!n.success)throw Error(n.message||"Failed to generate voucher codes");return n}async deleteVoucherCode(e){let t=this.cfg,r=`${t.basePath}${t.resourcePath}/delete-voucher-code`,o=await fetch(r,{method:"POST",headers:{"Content-Type":"application/json"},credentials:"same-origin",body:JSON.stringify({id:e})});if(!o.ok)throw Error(`Failed to delete voucher code: ${o.statusText}`)}getVoucherCodesExportUrl(e,t){let r=this.cfg,o=new URLSearchParams;return o.append("cartPriceRule",e.toString()),(null==t?void 0:t.start)!==void 0&&o.append("start",t.start.toString()),(null==t?void 0:t.limit)!==void 0&&o.append("limit",t.limit.toString()),`${r.basePath}${r.resourcePath}/export-voucher-codes?${o}`}async getCartItemConfig(){let e=this.cfg,t=`${e.basePath}${e.resourcePath}/get-cart-item-config`,r=await fetch(t,{method:"GET",headers:{"Content-Type":"application/json"},credentials:"same-origin"});if(!r.ok)throw Error(`Failed to get cart item config: ${r.statusText}`);return r.json()}}let u=new d({basePath:"/pimcore-studio/api",resourcePath:"/coreshop/cart_price_rules"}),p=null,h=null,f=async()=>p||h||(h=(async()=>{try{let e=await u.list();return p=(Array.isArray(e)?e:[]).map(e=>({value:e.id,label:e.name??String(e.id)})).filter(e=>null!=e.value&&e.label)}catch(e){return console.error("Failed to load cart price rules:",e),[]}finally{h=null}})());s.DynamicTypeObjectDataAbstractSelect;var m=r(8203),g=r(6239);class y{async getConfig(){let e=`${this.basePath}/coreshop/resource/config`,t=await fetch(e,{credentials:"same-origin"});if(!t.ok)throw Error(`Config request failed: ${t.status}`);return await t.json()}constructor(e="/pimcore-studio/api"){this.basePath=e}}class b{async getConfig(){return this.config?this.config:(this.loading||(this.loading=this.api.getConfig().then(e=>(this.config=e,this.loading=null,e))),this.loading)}async isClassAllowedForResource(e,t){let r=await this.getConfig(),o=e.split(".");if(2!==o.length)return!1;let[n,i]=o,a=r.stack[n];if(!a)return!1;let s=a[i];return!!s&&s.includes(t)}async getAllowedClasses(e){let t=await this.getConfig(),r=e.split(".");if(2!==r.length)return[];let[o,n]=r,i=t.stack[o];return i&&i[n]||[]}clearCache(){this.config=null,this.loading=null}constructor(){this.config=null,this.loading=null,this.api=new y}}b=(0,m.Cg)([(0,g._G)(),(0,m.Sn)("design:type",Function),(0,m.Sn)("design:paramtypes",[])],b),r(2028);class _{on(e,t){this.listeners.has(e)||this.listeners.set(e,[]),this.listeners.get(e).push(t)}off(e,t){let r=this.listeners.get(e);if(r){let e=r.indexOf(t);e>-1&&r.splice(e,1)}}emit(e){let t=this.editModeActive;"edit"===e?this.editModeActive=!0:("save"===e||"cancel"===e)&&(this.editModeActive=!1);let r=this.listeners.get(e);r&&r.forEach(e=>e()),t!==this.editModeActive&&this.notifyToolbarUpdate()}isEditModeActive(){return this.editModeActive}onToolbarUpdate(e){this.toolbarUpdateHandlers.push(e)}offToolbarUpdate(e){let t=this.toolbarUpdateHandlers.indexOf(e);t>-1&&this.toolbarUpdateHandlers.splice(t,1)}notifyToolbarUpdate(){this.toolbarUpdateHandlers.forEach(e=>e())}constructor(){this.listeners=new Map,this.editModeActive=!1,this.toolbarUpdateHandlers=[]}}new _;let E=null;Symbol.for("coreshop.order.cart_price_rule.condition_registry"),Symbol.for("coreshop.order.cart_price_rule.action_registry"),r(3090);var v=r(8144);let{Text:S}=a.Typography;class x{addListener(e,t,r){let o=arguments.length>3&&void 0!==arguments[3]&&arguments[3],n=arguments.length>4&&void 0!==arguments[4]?arguments[4]:0;this.listeners.has(e)||this.listeners.set(e,[]),this.listeners.get(e).push({callback:t,scope:r,once:o,priority:n})}addListenerOnce(e,t,r){this.addListener(e,t,r,!0,0)}removeListener(e,t){let r=this.listeners.get(e);if(!r)return;let o=r.findIndex(e=>e.callback===t);o>=0&&r.splice(o,1),0===r.length&&this.listeners.delete(e)}fireEvent(e){for(var t=arguments.length,r=Array(t>1?t-1:0),o=1;o<t;o++)r[o-1]=arguments[o];let n=this.listeners.get(e);if(n)for(let t of[...n].sort((e,t)=>e.priority-t.priority))t.callback.apply(t.scope,r),t.once&&this.removeListener(e,t.callback)}hasListeners(e){var t;return((null==(t=this.listeners.get(e))?void 0:t.length)??0)>0}constructor(){this.listeners=new Map}}!function(){window.__CORESHOP_BROKER__||(window.__CORESHOP_BROKER__=new x),window.__CORESHOP_BROKER__}();let w=(0,v.rU)(e=>{let{css:t}=e;return{container:t`
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
  `}}),$="coreshop_order",R=(0,v.rU)(e=>{let{css:t}=e;return{container:t`
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
  `}}),I="coreshop_cart",T=(0,v.rU)(e=>{let{css:t}=e;return{container:t`
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
  `}}),N="coreshop_quote";Symbol.for("coreshop.order.sale_tab_registry"),Symbol.for("coreshop.order.order_api"),Symbol.for("coreshop.order.cart_api"),Symbol.for("coreshop.order.quote_api"),(0,v.rU)(e=>{let{css:t,token:r}=e;return{toolbar:t`
    display: flex;
    gap: 8px;
    margin-bottom: 20px;
    padding: 12px;
    background: ${r.colorBgContainer};
    border: 1px solid ${r.colorBorderSecondary};
    border-radius: ${r.borderRadius}px;
  `}}),(0,v.rU)(e=>{let{css:t,token:r}=e;return{container:t`
    display: flex;
    flex-direction: column;
    height: 100%;
    overflow: auto;
    padding: 24px;
    background: ${r.colorBgElevated};
  `,topArea:t`
    margin-bottom: 20px;
  `,columnsArea:t`
    display: flex;
    gap: 20px;
    margin-bottom: 20px;
  `,leftColumn:t`
    flex: 0 0 50%;
    min-width: 0;
    display: flex;
    flex-direction: column;
    gap: 20px;
  `,rightColumn:t`
    flex: 0 0 50%;
    min-width: 0;
    display: flex;
    flex-direction: column;
    gap: 20px;
  `,bottomArea:t`
    margin-bottom: 0;
  `,block:t`
    /* Blocks are rendered with their own styling */
  `,emptyState:t`
    display: flex;
    align-items: center;
    justify-content: center;
    height: 100%;
    min-height: 400px;
  `,emptyStateText:t`
    color: ${r.colorTextTertiary};
    font-size: 14px;
  `}});let O=["coreshop-order-detail","coreshop-cart-detail","coreshop-quote-detail"];class k{supports(e){var t;return void 0!==e.component&&O.includes(e.component)&&"number"==typeof(null==(t=e.config)?void 0:t.orderId)}cleanConfig(e){var t;return{...e,config:{orderId:null==(t=e.config)?void 0:t.orderId}}}restore(e,t){var r;return"number"==typeof(null==(r=e.config)?void 0:r.orderId)&&!!(e.config.orderId>0)}}new k;class A{register(e,t){this.extensions.has(e)||this.extensions.set(e,[]),this.extensions.get(e).push(t)}getFields(e,t){return(this.extensions.get(e)||[]).map(e=>e(t))}hasExtensions(e){return this.extensions.has(e)&&this.extensions.get(e).length>0}constructor(){this.extensions=new Map}}A=(0,m.Cg)([(0,g._G)()],A),Symbol.for("CoreShop.Order.ModalFieldExtensionRegistry");class M{register(e,t){this.tabs.set(e,t)}get(e){return this.tabs.get(e)}has(e){return this.tabs.has(e)}getAll(){return Array.from(this.tabs.values())}getForType(e){return Array.from(this.tabs.values()).filter(t=>t.types.includes(e)).sort((e,t)=>e.priority-t.priority)}unregister(e){this.tabs.delete(e)}clear(){this.tabs.clear()}constructor(){this.tabs=new Map}}M=(0,m.Cg)([(0,g._G)()],M),(0,v.rU)(e=>{let{css:t,token:r}=e;return{container:t`
    display: flex;
    flex-direction: column;
    gap: 0;
  `,row:t`
    display: flex;
    gap: 0;
  `,cell:t`
    flex: 1;
    padding: 20px;
    background: ${r.colorBgContainer};
    border: 1px solid ${r.colorBorder};
    border-right-width: 0;

    &:last-child {
      border-right-width: 1px;
    }
  `,stateInfo:t`
    display: flex;
    flex-direction: column;
    gap: 4px;
  `,stateLabel:t`
    font-size: 12px;
    color: ${r.colorTextSecondary};
  `,stateValue:t`
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 14px;
    font-weight: 500;
  `,colorDot:t`
    width: 10px;
    height: 10px;
    border-radius: 50%;
    display: inline-block;
  `,infoLabel:t`
    font-size: 12px;
    color: ${r.colorTextSecondary};
    margin-bottom: 4px;
  `,infoValueBig:t`
    font-size: 18px;
    font-weight: 600;
    color: ${r.colorText};
  `}}),(0,v.rU)(e=>{let{css:t,token:r}=e;return{card:t`
    .ant-card-head {
      background: ${r.colorBgContainer};
      border-bottom: 1px solid ${r.colorBorderSecondary};
    }
  `,container:t`
    display: flex;
    flex-direction: column;
    gap: 0;
  `,infoSection:t`
    display: flex;
    flex-direction: column;
    padding: 16px;
    border-bottom: 1px solid ${r.colorBorderSecondary};
  `,infoItem:t`
    display: flex;
    flex-direction: column;
    gap: 4px;

    strong {
      font-weight: 500;
      color: ${r.colorTextSecondary};
    }
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
    justify-content: space-between;
    gap: 16px;
    padding: 16px;
  `,addressText:t`
    flex: 1;
    white-space: pre-line;
    line-height: 1.6;
  `,openButtonContainer:t`
    flex-shrink: 0;
  `,openButton:t`
    /* Button styling */
  `,noData:t`
    padding: 16px;
    color: ${r.colorTextTertiary};
  `}}),(0,v.rU)(e=>{let{css:t,token:r}=e;return{container:t`
    /* No extra padding needed - Card handles it */
  `,card:t`
    .ant-card-head {
      background: ${r.colorBgContainer};
      border-bottom: 1px solid ${r.colorBorderSecondary};
    }

    .ant-card-body {
      padding: 0;
    }
  `,table:t`
    .ant-table-thead > tr > th {
      background: ${r.colorBgContainer};
      font-weight: 600;
    }

    .ant-table {
      margin-bottom: 0;
    }
  `,priceRulesSection:t`
    margin-top: 16px;
    padding-top: 16px;
    border-top: 1px solid ${r.colorBorderSecondary};
  `,priceRulesTable:t`
    .ant-table-tbody > tr > td {
      border-bottom: none;
      padding: 8px 16px;
    }
  `,summarySection:t`
    margin-top: 24px;
    padding: 20px;
    background: ${r.colorBgLayout};
    border-top: 1px solid ${r.colorBorderSecondary};
    display: flex;
    justify-content: flex-end;
  `,summaryWrapper:t`
    min-width: 320px;
    max-width: 400px;
  `,summaryTable:t`
    background: transparent;

    .ant-table {
      background: transparent;
    }

    .ant-table-tbody > tr > td {
      border-bottom: 1px dashed ${r.colorBorderSecondary};
      padding: 10px 0;
      background: transparent;
    }

    .ant-table-tbody > tr:last-child > td {
      border-bottom: none;
      padding-top: 14px;
      border-top: 2px solid ${r.colorBorder};
    }
  `}}),(0,v.rU)(e=>{let{css:t,token:r}=e;return{content:t`
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
  `}}),(0,v.rU)(e=>{let{css:t,token:r}=e;return{content:t`
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
  `}}),(0,v.rU)(e=>{let{css:t,token:r}=e;return{content:t`
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
  `}}),(0,v.rU)(e=>{let{css:t,token:r}=e;return{section:t`
    margin-bottom: 24px;
  `,sectionHeader:t`
    font-size: 16px;
    font-weight: 600;
    margin-bottom: 16px;
    color: ${r.colorText};
    display: flex;
    align-items: center;
    gap: 8px;

    &:before {
      content: '';
      display: inline-block;
      width: 20px;
      height: 20px;
      background: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="%2352c41a"><path d="M3 13.5L2.25 12H7.5L6 9.5H17L15 12H22L20.5 10V19H22V21H2V19H3.5V13.5H3ZM5.5 19H18.5V12.5H14.5L16.5 10H7.5L9 12.5H5.5V19Z"/></svg>') no-repeat center;
      background-size: contain;
    }
  `,table:t`
    .ant-table-thead > tr > th {
      background: ${r.colorBgContainer};
      font-weight: 600;
    }
  `}}),(0,v.rU)(e=>{let{css:t,token:r}=e;return{modal:t`
    .ant-modal-body {
      padding: 0;
    }
  `,form:t`
    .ant-tabs {
      margin: 0;
    }
    .ant-tabs-nav {
      margin: 0;
      padding: 0 24px;
      background: ${r.colorBgContainer};
    }
  `,content:t`
    padding: 24px;
  `,table:t`
    margin-top: 16px;

    .ant-table-thead > tr > th {
      background: ${r.colorBgContainer};
      font-weight: 600;
    }
  `}}),r(835);var P=r(5599);class C extends P.G{}new C({basePath:"/pimcore-studio/api",resourcePath:"/coreshop/payment_providers"});let D=null,j=null,{TextArea:L}=((0,v.rU)(e=>{let{css:t,token:r}=e;return{modal:t`
    .ant-modal-body {
      padding-top: 24px;
    }
  `,form:t`
    .ant-form-item-label > label.ant-form-item-required:not(.ant-form-item-required-mark-optional)::before {
      color: ${r.colorError};
    }
  `}}),(0,v.rU)(e=>{let{css:t,token:r}=e;return{card:t`
    .ant-card-head {
      background: ${r.colorBgContainer};
      border-bottom: 1px solid ${r.colorBorderSecondary};
    }
  `,table:t`
    .ant-table-thead > tr > th {
      background: ${r.colorBgContainer};
      font-weight: 600;
    }
  `,detailContent:t`
    padding: 16px 0;
  `,detailRow:t`
    display: flex;
    padding: 12px 0;
    border-bottom: 1px solid ${r.colorBorder};

    &:last-of-type {
      border-bottom: none;
    }
  `,detailLabel:t`
    width: 180px;
    font-weight: 500;
    color: ${r.colorTextSecondary};
    flex-shrink: 0;
  `,detailValue:t`
    flex: 1;
    color: ${r.colorText};
  `,detailsHeader:t`
    font-size: 16px;
    font-weight: 600;
    margin: 24px 0 12px 0;
    color: ${r.colorText};
  `,detailRowBody:t`
    padding: 12px;
    background: ${r.colorBgLayout};
    white-space: normal;
    word-wrap: break-word;
  `}}),(0,v.rU)(e=>{let{css:t,token:r}=e;return{card:t`
    .ant-card-head {
      background: ${r.colorBgContainer};
      border-bottom: 1px solid ${r.colorBorderSecondary};
    }
  `,table:t`
    .ant-table-thead > tr > th {
      background: ${r.colorBgContainer};
      font-weight: 600;
    }
  `}}),(0,v.rU)(e=>{let{css:t,token:r}=e;return{card:t`
    .ant-card-head {
      background: ${r.colorBgContainer};
      border-bottom: 1px solid ${r.colorBorderSecondary};
    }
  `,table:t`
    .ant-table-thead > tr > th {
      background: ${r.colorBgContainer};
      font-weight: 600;
    }
  `}}),a.Input);(0,v.rU)(e=>{let{css:t,token:r}=e;return{card:t`
    .ant-card-head {
      background: ${r.colorBgContainer};
      border-bottom: 1px solid ${r.colorBorderSecondary};
    }
  `,emptyState:t`
    padding: 40px 20px;
    text-align: center;
    color: ${r.colorTextTertiary};
    font-style: italic;
  `,commentsList:t`
    display: flex;
    flex-direction: column;
    max-height: 400px;
    overflow-y: auto;
  `,commentItem:t`
    padding: 16px;
    border-bottom: 1px dashed ${r.colorBorder};

    &:last-child {
      border-bottom: none;
    }
  `,commentHeader:t`
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 8px;
  `,commentMeta:t`
    flex: 1;
  `,commentDate:t`
    font-size: 13px;
    color: ${r.colorTextSecondary};
    font-style: italic;
  `,commentText:t`
    font-size: 14px;
    color: ${r.colorText};
    margin-bottom: 8px;
    white-space: pre-wrap;
    word-wrap: break-word;
  `,commentBadge:t`
    display: flex;
    align-items: center;
    font-size: 12px;
    color: #722ed1;
    margin-top: 8px;
  `,commentBadgeAdmin:t`
    display: flex;
    align-items: center;
    font-size: 12px;
    color: ${r.colorTextSecondary};
    margin-top: 8px;
  `}}),(0,v.rU)(e=>{let{css:t,token:r}=e;return{card:t`
    .ant-card-head {
      background: ${r.colorBgContainer};
      border-bottom: 1px solid ${r.colorBorderSecondary};
    }

    .ant-card-body {
      padding: 0;
    }

    .ant-table {
      .ant-table-thead > tr > th {
        background: ${r.colorBgContainer};
        font-weight: 600;
      }
    }
  `,expandedRow:t`
    padding: 12px 24px;
    background: ${r.colorBgLayout};
    color: ${r.colorTextSecondary};
    font-size: 13px;
  `,cancelButton:t`
    &:hover {
      background-color: #c72a2a !important;
      border-color: #c72a2a !important;
    }
  `}}),(0,v.rU)(e=>{let{css:t,token:r}=e;return{card:t`
    .ant-card-head {
      background: ${r.colorBgContainer};
      border-bottom: 1px solid ${r.colorBorderSecondary};
    }
  `,table:t`
    .ant-table-thead > tr > th {
      background: ${r.colorBgContainer};
      font-weight: 600;
    }
  `,modal:t`
    .ant-modal-body {
      padding: 0;
    }
  `,iframeContainer:t`
    width: 100%;
    height: 500px;
    overflow: hidden;
  `,iframe:t`
    width: 100%;
    height: 100%;
    border: none;
  `}});class H{register(e,t){this.steps.set(e,t)}get(e){return this.steps.get(e)}has(e){return this.steps.has(e)}getAll(){return Array.from(this.steps.values())}getSorted(){return this.getAll().sort((e,t)=>e.priority-t.priority)}unregister(e){this.steps.delete(e)}clear(){this.steps.clear()}constructor(){this.steps=new Map}}function F(e){let t=new URLSearchParams;for(let[r,o]of Object.entries(e))"items"===r&&Array.isArray(o)?o.forEach((e,r)=>{t.append(`items[${r}][product]`,String(e.product)),t.append(`items[${r}][quantity]`,String(e.quantity)),void 0!==e.customItemPrice&&t.append(`items[${r}][customItemPrice]`,String(e.customItemPrice)),void 0!==e.customItemDiscount&&t.append(`items[${r}][customItemDiscount]`,String(e.customItemDiscount)),void 0!==e.unitDefinition&&t.append(`items[${r}][unitDefinition]`,String(e.unitDefinition))}):null!=o&&t.append(r,String(o));return t}H=(0,m.Cg)([(0,g._G)()],H),Symbol.for("CoreShop/Order/OrderCreation/StepRegistry");class U{async getCustomerDetails(e){let t=`${this.basePath}/get-customer-details?customerId=${e}`,r=await fetch(t,{method:"GET",headers:{"Content-Type":"application/json"},credentials:"same-origin"});if(!r.ok)throw Error("Failed to fetch customer details");let o=await r.json();if(!o.success)throw Error("string"==typeof o.message?o.message:"Failed to fetch customer");if(!o.customer)throw Error("Customer not found in response");return o.customer}async preview(e){let t=F(e),r=await fetch(`${this.basePath}/preview`,{method:"POST",headers:{"Content-Type":"application/x-www-form-urlencoded"},credentials:"same-origin",body:t});if(!r.ok)throw Error("Preview request failed");let o=await r.json();if(!o.success)throw Error("string"==typeof o.message?o.message:o.message?JSON.stringify(o.message):"Preview failed");if(!o.data)throw Error("Preview data not found in response");return o.data}async create(e){let t=F(e),r=await fetch(`${this.basePath}/create`,{method:"POST",headers:{"Content-Type":"application/x-www-form-urlencoded"},credentials:"same-origin",body:t});if(!r.ok)throw Error("Create request failed");let o=await r.json();if(!o.success)throw Error("string"==typeof o.message?o.message:o.message?JSON.stringify(o.message):"Create failed");if(void 0===o.id)throw Error("Created ID not found in response");return{success:!0,id:o.id}}constructor(){this.basePath="/pimcore-studio/api/coreshop/order-creation"}}new U;let B=null;(0,v.rU)(e=>{let{css:t}=e;return{selectorCard:t`
    max-width: 600px;
    margin: 0 auto;
  `,selectButton:t`
    min-width: 200px;
  `}});let V=(0,v.rU)(e=>{let{css:t,token:r}=e;return{container:t`
    display: flex;
    flex-direction: column;
    height: 100%;
    background: ${r.colorBgContainer};
    overflow: hidden;
  `,toolbar:t`
    display: flex;
    justify-content: space-between;
    padding: 12px 16px;
    border-bottom: 1px solid ${r.colorBorderSecondary};
    flex-shrink: 0;
    background: ${r.colorBgContainer};
  `,content:t`
    flex: 1;
    overflow: auto;
    padding: 16px;
  `,stepsContainer:t`
    display: flex;
    flex-direction: column;
    gap: 16px;
  `,stepWrapper:t`
    /* Step wrapper styles */
  `}});new l.G$({basePath:"/pimcore-studio/api",resourcePath:"/coreshop/stores"}),r(199);let G=null,q=null,z=null,W=null,K=null,Y=null;(0,v.rU)(e=>{let{css:t,token:r}=e;return{emptyState:t`
    padding: 32px;
    text-align: center;
    background: ${r.colorBgLayout};
    border-radius: ${r.borderRadius}px;
  `,addButton:t`
    margin-top: 16px;
  `}})},199:function(e,t,r){r.d(t,{r:()=>i});var o=r(2855);r(5168);var n=r(2696);let i=e=>{let{accept:t,onDrop:r,className:i,disabled:a,isValidData:s,children:l}=e,c=Array.isArray(t)?t:[t];return(0,o.jsx)(n.Droppable,{className:i,disabled:a,isValidContext:function(e){return c.includes(e.type)},isValidData:e=>!s||s(e),onDrop:r,children:l})}},6911:function(e,t,r){var o,n,i,a,s,l,c,d,u,p,h,f,m=r(7945),g=r.n(m);let y=[],b={"@pimcore/studio-ui-bundle":[{alias:"@pimcore/studio-ui-bundle",externalType:"promise",shareScope:"default"}]},_="coreshoporder",E="version-first";if((r.initializeSharingData||r.initializeExposesData)&&r.federation){let e=(e,t,r)=>{e&&e[t]&&(e[t]=r)},t=(e,t,r)=>{var o,n,i,a,s,l;let c=r();Array.isArray(c)?(null!=(i=(o=e)[n=t])||(o[n]=[]),e[t].push(...c)):"object"==typeof c&&null!==c&&(null!=(l=(a=e)[s=t])||(a[s]={}),Object.assign(e[t],c))},m=(e,t,r)=>{var o,n,i;null!=(i=(o=e)[n=t])||(o[n]=r())},v=null!=(d=null==(o=r.remotesLoadingData)?void 0:o.chunkMapping)?d:{},S=null!=(u=null==(n=r.remotesLoadingData)?void 0:n.moduleIdToRemoteDataMapping)?u:{},x=null!=(p=null==(i=r.initializeSharingData)?void 0:i.scopeToSharingDataMapping)?p:{},w=null!=(h=null==(a=r.consumesLoadingData)?void 0:a.chunkMapping)?h:{},$=null!=(f=null==(s=r.consumesLoadingData)?void 0:s.moduleIdToConsumeDataMapping)?f:{},R={},I=[],T={},N=null==(l=r.initializeExposesData)?void 0:l.shareScope;for(let e in g())r.federation[e]=g()[e];m(r.federation,"consumesLoadingModuleToHandlerMapping",()=>{let e={};for(let[t,r]of Object.entries($))e[t]={getter:r.fallback,shareInfo:{shareConfig:{fixedDependencies:!1,requiredVersion:r.requiredVersion,strictVersion:r.strictVersion,singleton:r.singleton,eager:r.eager},scope:[r.shareScope]},shareKey:r.shareKey};return e}),m(r.federation,"initOptions",()=>({})),m(r.federation.initOptions,"name",()=>_),m(r.federation.initOptions,"shareStrategy",()=>E),m(r.federation.initOptions,"shared",()=>{let e={};for(let[t,r]of Object.entries(x))for(let o of r)if("object"==typeof o&&null!==o){let{name:r,version:n,factory:i,eager:a,singleton:s,requiredVersion:l,strictVersion:c}=o,d={},u=function(e){return void 0!==e};u(s)&&(d.singleton=s),u(l)&&(d.requiredVersion=l),u(a)&&(d.eager=a),u(c)&&(d.strictVersion=c);let p={version:n,scope:[t],shareConfig:d,get:i};e[r]?e[r].push(p):e[r]=[p]}return e}),t(r.federation.initOptions,"remotes",()=>Object.values(b).flat().filter(e=>"script"===e.externalType)),t(r.federation.initOptions,"plugins",()=>y),m(r.federation,"bundlerRuntimeOptions",()=>({})),m(r.federation.bundlerRuntimeOptions,"remotes",()=>({})),m(r.federation.bundlerRuntimeOptions.remotes,"chunkMapping",()=>v),m(r.federation.bundlerRuntimeOptions.remotes,"remoteInfos",()=>b),m(r.federation.bundlerRuntimeOptions.remotes,"idToExternalAndNameMapping",()=>{let e={};for(let[t,r]of Object.entries(S))e[t]=[r.shareScope,r.name,r.externalModuleId,r.remoteName];return e}),m(r.federation.bundlerRuntimeOptions.remotes,"webpackRequire",()=>r),t(r.federation.bundlerRuntimeOptions.remotes,"idToRemoteMap",()=>{let e={};for(let[t,r]of Object.entries(S)){let o=b[r.remoteName];o&&(e[t]=o)}return e}),e(r,"S",r.federation.bundlerRuntime.S),r.federation.attachShareScopeMap&&r.federation.attachShareScopeMap(r),e(r.f,"remotes",(e,t)=>r.federation.bundlerRuntime.remotes({chunkId:e,promises:t,chunkMapping:v,idToExternalAndNameMapping:r.federation.bundlerRuntimeOptions.remotes.idToExternalAndNameMapping,idToRemoteMap:r.federation.bundlerRuntimeOptions.remotes.idToRemoteMap,webpackRequire:r})),e(r.f,"consumes",(e,t)=>r.federation.bundlerRuntime.consumes({chunkId:e,promises:t,chunkMapping:w,moduleToHandlerMapping:r.federation.consumesLoadingModuleToHandlerMapping,installedModules:R,webpackRequire:r})),e(r,"I",(e,t)=>r.federation.bundlerRuntime.I({shareScopeName:e,initScope:t,initPromises:I,initTokens:T,webpackRequire:r})),e(r,"initContainer",(e,t,o)=>r.federation.bundlerRuntime.initContainerEntry({shareScope:e,initScope:t,remoteEntryInitOptions:o,shareScopeKey:N,webpackRequire:r})),e(r,"getContainer",(e,t)=>{var o=r.initializeExposesData.moduleMap;return r.R=t,t=Object.prototype.hasOwnProperty.call(o,e)?o[e]():Promise.resolve().then(()=>{throw Error('Module "'+e+'" does not exist in container.')}),r.R=void 0,t}),r.federation.instance=r.federation.runtime.init(r.federation.initOptions),(null==(c=r.consumesLoadingData)?void 0:c.initialConsumes)&&r.federation.bundlerRuntime.installInitialConsumes({webpackRequire:r,installedModules:R,initialConsumes:r.consumesLoadingData.initialConsumes,moduleToHandlerMapping:r.federation.consumesLoadingModuleToHandlerMapping})}},5698:function(e){e.exports=new Promise(e=>{let t=window.StudioUIBundleRemoteUrl,r=document.createElement("script"),o=!1;(document.querySelectorAll("script").forEach(e=>{if(e.src.replace(/https?:\/\/[^/]+/,"")===t.replace(/https?:\/\/[^/]+/,"")){o=!0;return}}),o)?e({get:e=>window.pimcore_studio_ui_bundle.get(e),init:(...e)=>{try{return window.pimcore_studio_ui_bundle.init(...e)}catch(e){console.log("remote container already initialized")}}}):(r.src=t,r.onload=()=>{e({get:e=>window.pimcore_studio_ui_bundle.get(e),init:(...e)=>{try{return window.pimcore_studio_ui_bundle.init(...e)}catch(e){console.log("remote container already initialized")}}})},document.head.appendChild(r))})}},__webpack_module_cache__={};function __webpack_require__(e){var t=__webpack_module_cache__[e];if(void 0!==t)return t.exports;var r=__webpack_module_cache__[e]={exports:{}};return __webpack_modules__[e].call(r.exports,r,r.exports,__webpack_require__),r.exports}__webpack_require__.m=__webpack_modules__,__webpack_require__.c=__webpack_module_cache__,__webpack_require__.x=()=>{var e=__webpack_require__.O(void 0,["448","387","729","290","536","57","515","84"],()=>__webpack_require__(8920));return __webpack_require__.O(e)},(()=>{__webpack_require__.federation||(__webpack_require__.federation={chunkMatcher:function(e){return!/^(57|795|84)$/.test(e)},rootOutputDir:"../../"})})(),(()=>{__webpack_require__.n=e=>{var t=e&&e.__esModule?()=>e.default:()=>e;return __webpack_require__.d(t,{a:t}),t}})(),(()=>{__webpack_require__.d=(e,t)=>{for(var r in t)__webpack_require__.o(t,r)&&!__webpack_require__.o(e,r)&&Object.defineProperty(e,r,{enumerable:!0,get:t[r]})}})(),(()=>{__webpack_require__.f={},__webpack_require__.e=e=>Promise.all(Object.keys(__webpack_require__.f).reduce((t,r)=>(__webpack_require__.f[r](e,t),t),[]))})(),(()=>{__webpack_require__.u=e=>"static/js/async/"+e+"."+({232:"01c28649",375:"0e6c9209",376:"17d02985",407:"b5c6975f",460:"3338a81f",63:"204cfe8f",695:"7797e0e3",718:"8bee8ab6",76:"c1e9f242",760:"d7eae9df",79:"f3d624ca",920:"b1a3d2d7"})[e]+".js"})(),(()=>{__webpack_require__.miniCssF=e=>""+e+".css"})(),(()=>{__webpack_require__.g=(()=>{if("object"==typeof globalThis)return globalThis;try{return this||Function("return this")()}catch(e){if("object"==typeof window)return window}})()})(),(()=>{__webpack_require__.o=(e,t)=>Object.prototype.hasOwnProperty.call(e,t)})(),(()=>{var e={},t="coreshoporder:";__webpack_require__.l=function(r,o,n,i){if(e[r])return void e[r].push(o);if(void 0!==n)for(var a,s,l=document.getElementsByTagName("script"),c=0;c<l.length;c++){var d=l[c];if(d.getAttribute("src")==r||d.getAttribute("data-webpack")==t+n){a=d;break}}a||(s=!0,(a=document.createElement("script")).timeout=120,__webpack_require__.nc&&a.setAttribute("nonce",__webpack_require__.nc),a.setAttribute("data-webpack",t+n),a.src=r),e[r]=[o];var u=function(t,o){a.onerror=a.onload=null,clearTimeout(p);var n=e[r];if(delete e[r],a.parentNode&&a.parentNode.removeChild(a),n&&n.forEach(function(e){return e(o)}),t)return t(o)},p=setTimeout(u.bind(null,void 0,{type:"timeout",target:a}),12e4);a.onerror=u.bind(null,a.onerror),a.onload=u.bind(null,a.onload),s&&document.head.appendChild(a)}})(),(()=>{__webpack_require__.r=e=>{"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(e,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(e,"__esModule",{value:!0})}})(),(()=>{var e=[];__webpack_require__.O=(t,r,o,n)=>{if(r){n=n||0;for(var i=e.length;i>0&&e[i-1][2]>n;i--)e[i]=e[i-1];e[i]=[r,o,n];return}for(var a=1/0,i=0;i<e.length;i++){for(var[r,o,n]=e[i],s=!0,l=0;l<r.length;l++)(!1&n||a>=n)&&Object.keys(__webpack_require__.O).every(e=>__webpack_require__.O[e](r[l]))?r.splice(l--,1):(s=!1,n<a&&(a=n));if(s){e.splice(i--,1);var c=o();void 0!==c&&(t=c)}}return t}})(),(()=>{__webpack_require__.p="/bundles/coreshoporder/studio/aa314d84-fcea-4702-8f39-c6d1f25116b4/"})(),(()=>{__webpack_require__.S={},__webpack_require__.initializeSharingData={scopeToSharingDataMapping:{default:[{name:"@coreshop/resource",version:"1.0.0",factory:()=>Promise.all([__webpack_require__.e("448"),__webpack_require__.e("387"),__webpack_require__.e("729"),__webpack_require__.e("57"),__webpack_require__.e("515"),__webpack_require__.e("718")]).then(()=>()=>__webpack_require__(5795)),eager:0,singleton:1,requiredVersion:"*",strictVersion:0},{name:"@emotion/react",version:"11.14.0",factory:()=>Promise.all([__webpack_require__.e("387"),__webpack_require__.e("57"),__webpack_require__.e("79")]).then(()=>()=>__webpack_require__(3095)),eager:0,singleton:1,requiredVersion:"*",strictVersion:0},{name:"antd",version:"5.29.1",factory:()=>Promise.all([__webpack_require__.e("448"),__webpack_require__.e("407"),__webpack_require__.e("376"),__webpack_require__.e("57"),__webpack_require__.e("795")]).then(()=>()=>__webpack_require__(3)),eager:0,singleton:1,requiredVersion:"*"},{name:"i18next",version:"23.16.8",factory:()=>__webpack_require__.e("760").then(()=>()=>__webpack_require__(5731)),eager:0,singleton:1,requiredVersion:"*",strictVersion:0},{name:"react-dom",version:"18.3.1",factory:()=>Promise.all([__webpack_require__.e("920"),__webpack_require__.e("57")]).then(()=>()=>__webpack_require__(3763)),eager:0,singleton:1,requiredVersion:"*",strictVersion:0},{name:"react-i18next",version:"14.1.3",factory:()=>Promise.all([__webpack_require__.e("63"),__webpack_require__.e("57")]).then(()=>()=>__webpack_require__(2882)),eager:0,singleton:1,requiredVersion:"*",strictVersion:0},{name:"react/jsx-runtime",version:"18.3.1",factory:()=>Promise.all([__webpack_require__.e("57"),__webpack_require__.e("76")]).then(()=>()=>__webpack_require__(9242)),eager:0,singleton:1,requiredVersion:"*",strictVersion:0},{name:"react",version:"18.3.1",factory:()=>__webpack_require__.e("375").then(()=>()=>__webpack_require__(4658)),eager:0,singleton:1,requiredVersion:"*",strictVersion:0},5698]},uniqueName:"coreshoporder"},__webpack_require__.I=__webpack_require__.I||function(){throw Error("should have __webpack_require__.I")}})(),(()=>{__webpack_require__.consumesLoadingData={chunkMapping:{795:["993"],889:["5572"],84:["3933"],57:["5168"],515:["5493","5210","8701","2855"]},moduleIdToConsumeDataMapping:{3933:{shareScope:"default",shareKey:"@coreshop/resource",import:"@coreshop/resource",requiredVersion:"*",strictVersion:!1,singleton:!0,eager:!1,fallback:()=>__webpack_require__.e("232").then(()=>()=>__webpack_require__(5795))},5210:{shareScope:"default",shareKey:"react-i18next",import:"react-i18next",requiredVersion:"*",strictVersion:!1,singleton:!0,eager:!1,fallback:()=>__webpack_require__.e("63").then(()=>()=>__webpack_require__(2882))},2855:{shareScope:"default",shareKey:"react/jsx-runtime",import:"react/jsx-runtime",requiredVersion:"*",strictVersion:!1,singleton:!0,eager:!1,fallback:()=>__webpack_require__.e("695").then(()=>()=>__webpack_require__(9242))},993:{shareScope:"default",shareKey:"react-dom",import:"react-dom",requiredVersion:"*",strictVersion:!1,singleton:!0,eager:!1,fallback:()=>__webpack_require__.e("920").then(()=>()=>__webpack_require__(3763))},5572:{shareScope:"default",shareKey:"i18next",import:"i18next",requiredVersion:"*",strictVersion:!1,singleton:!0,eager:!1,fallback:()=>__webpack_require__.e("760").then(()=>()=>__webpack_require__(5731))},5168:{shareScope:"default",shareKey:"react",import:"react",requiredVersion:"*",strictVersion:!1,singleton:!0,eager:!1,fallback:()=>__webpack_require__.e("375").then(()=>()=>__webpack_require__(4658))},8701:{shareScope:"default",shareKey:"antd",import:"antd",requiredVersion:"*",strictVersion:!1,singleton:!0,eager:!1,fallback:()=>Promise.all([__webpack_require__.e("407"),__webpack_require__.e("376"),__webpack_require__.e("795")]).then(()=>()=>__webpack_require__(3))},5493:{shareScope:"default",shareKey:"@emotion/react",import:"@emotion/react",requiredVersion:"*",strictVersion:!1,singleton:!0,eager:!1,fallback:()=>__webpack_require__.e("460").then(()=>()=>__webpack_require__(3095))}},initialConsumes:["5168","5493","5210","8701","2855","3933","5572"]},__webpack_require__.f.consumes=__webpack_require__.f.consumes||function(){throw Error("should have __webpack_require__.f.consumes")}})(),(()=>{var e={57:0,84:0,889:0};__webpack_require__.f.j=function(t,r){var o=__webpack_require__.o(e,t)?e[t]:void 0;if(0!==o)if(o)r.push(o[2]);else if(/^(57|795|84)$/.test(t))e[t]=0;else{var n=new Promise((r,n)=>o=e[t]=[r,n]);r.push(o[2]=n);var i=__webpack_require__.p+__webpack_require__.u(t),a=Error(),s=function(r){if(__webpack_require__.o(e,t)&&(0!==(o=e[t])&&(e[t]=void 0),o)){var n=r&&("load"===r.type?"missing":r.type),i=r&&r.target&&r.target.src;a.message="Loading chunk "+t+" failed.\n("+n+": "+i+")",a.name="ChunkLoadError",a.type=n,a.request=i,o[1](a)}};__webpack_require__.l(i,s,"chunk-"+t,t)}},__webpack_require__.O.j=t=>0===e[t];var t=(t,r)=>{var o,n,[i,a,s]=r,l=0;if(i.some(t=>0!==e[t])){for(o in a)__webpack_require__.o(a,o)&&(__webpack_require__.m[o]=a[o]);if(s)var c=s(__webpack_require__)}for(t&&t(r);l<i.length;l++)n=i[l],__webpack_require__.o(e,n)&&e[n]&&e[n][0](),e[n]=0;return __webpack_require__.O(c)},r=self.webpackChunkcoreshoporder=self.webpackChunkcoreshoporder||[];r.forEach(t.bind(null,0)),r.push=t.bind(null,r.push.bind(r))})(),(()=>{__webpack_require__.remotesLoadingData={chunkMapping:{84:["2028","3842"],515:["2977","2696","2703"],889:["3090","4781"]},moduleIdToRemoteDataMapping:{2028:{shareScope:"default",name:"./modules/widget-manager",externalModuleId:5698,remoteName:"@pimcore/studio-ui-bundle"},2696:{shareScope:"default",name:"./components",externalModuleId:5698,remoteName:"@pimcore/studio-ui-bundle"},2703:{shareScope:"default",name:"./modules/app",externalModuleId:5698,remoteName:"@pimcore/studio-ui-bundle"},3842:{shareScope:"default",name:"./modules/element",externalModuleId:5698,remoteName:"@pimcore/studio-ui-bundle"},2977:{shareScope:"default",name:".",externalModuleId:5698,remoteName:"@pimcore/studio-ui-bundle"},3090:{shareScope:"default",name:"./modules/data-object",externalModuleId:5698,remoteName:"@pimcore/studio-ui-bundle"},4781:{shareScope:"default",name:"./app",externalModuleId:5698,remoteName:"@pimcore/studio-ui-bundle"}}},__webpack_require__.f.remotes=__webpack_require__.f.remotes||function(){throw Error("should have __webpack_require__.f.remotes")}})(),(()=>{var e=__webpack_require__.x,t=!1;__webpack_require__.x=function(){if(t||(t=!0,__webpack_require__(6911)),"function"==typeof e)return e();console.warn("[MF] Invalid prevStartup")}})();var __webpack_exports__=__webpack_require__.x()})();