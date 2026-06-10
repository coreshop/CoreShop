/*! For license information please see __federation_expose_default_export.9a0dab4e.js.LICENSE.txt */
"use strict";(self["chunk_coreshopstore "]=self["chunk_coreshopstore "]||[]).push([["525"],{7350:function(e,t,a){a.d(t,{u:()=>h});var n=a(5120),l=a(7185),r=a.n(l),i=a(6147),o=a(8421),d=a(5017),s=a(2162),c=a(2696),u=a(199);let p=(0,a(9).rU)(e=>{let{token:t,css:a}=e;return{tree:a`
    padding: ${t.paddingXS}px;
    background: transparent;

    .ant-tree-treenode {
      display: flex;
      align-items: center;
      width: 100%;
      padding: 1px 0;
    }

    .ant-tree-node-content-wrapper {
      flex: 1 1 auto;
      width: auto;
      white-space: nowrap;
      display: flex;
      align-items: center;
      border-radius: ${t.borderRadiusSM}px;
      transition: background-color 0.2s;
      padding: 3px 6px;
      line-height: 22px;
    }

    .ant-tree-title {
      display: inline-flex;
      align-items: center;
      overflow: hidden;
      text-overflow: ellipsis;
      width: 100%;
    }

    .ant-tree-switcher {
      width: 18px;
      line-height: 22px;
    }

    @media (hover: hover) {
      .ant-tree-node-content-wrapper:hover {
        background-color: ${t.colorFillQuaternary};
      }
    }

    .ant-tree-node-content-wrapper:focus {
      outline: none;
      background-color: ${t.colorFillQuaternary};
    }

    .ant-tree-node-content-wrapper.ant-tree-node-selected {
      background-color: ${t.colorPrimaryBg};
    }
  `,droppableInline:a`
    display: inline-flex;
    align-items: center;
    gap: 8px;
  `,contentPadding:a`
    padding: ${t.paddingSM}px;
  `,leafNode:a`
    display: inline-flex;
    align-items: center;
    gap: 6px;
  `,leafIcon:a`
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 16px;
    height: 16px;
    flex-shrink: 0;
    color: ${t.colorTextTertiary};
  `,groupNode:a`
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-weight: 500;
  `,groupCount:a`
    font-size: 11px;
    color: ${t.colorTextQuaternary};
    font-weight: 400;
  `,inactive:a`
    color: ${t.colorTextDisabled};
    text-decoration: line-through;
  `,inactiveTag:a`
    margin-left: 4px;
    font-size: ${t.fontSizeSM-1}px;
    line-height: ${t.fontSizeSM+4}px;
    padding: 0 4px;
    border-radius: ${t.borderRadiusSM}px;
  `}}),h=e=>{let{items:t,groups:a,loading:l,rootTitle:h,addLabel:g,leafIcon:f="widget-default",resolveGroupId:x,onReload:v,onAdd:y,onDelete:m,onSelect:j,onMove:b,buildDragInfo:w,dragType:S}=e,{t:C}=(0,i.useTranslation)(),{styles:M}=p(),k=(e,t)=>{let a=(0,n.jsxs)("span",{className:M.groupNode,children:[(0,n.jsx)(c.Icon,{value:"folder"}),(0,n.jsx)("span",{children:e.name}),(0,n.jsxs)("span",{className:M.groupCount,children:["(",t,")"]})]});return S&&b?(0,n.jsx)(u.r,{className:M.droppableInline,accept:S,isValidData:e=>{var t;return"number"==typeof(null==e||null==(t=e.data)?void 0:t.id)},onDrop:t=>{var a;let n=null==t||null==(a=t.data)?void 0:a.id;"number"==typeof n&&b(n,null===e.id?null:e.id)},children:a}):a},D=r().useMemo(()=>{let e=e=>{let t,a;return{key:e.id,title:(t=!1===e.active,a=(0,n.jsx)(c.Dropdown,{trigger:["contextMenu"],menu:{items:[{key:"delete",icon:(0,n.jsx)(c.Icon,{value:"trash"}),label:C("toolbar.delete",{defaultValue:"Delete"}),onClick:()=>m(e.id)}]},children:(0,n.jsxs)("span",{className:`${M.leafNode} ${t?M.inactive:""}`,children:[(0,n.jsx)("span",{className:M.leafIcon,children:(0,n.jsx)(c.Icon,{value:f})}),e.name,t&&(0,n.jsx)(o.A,{className:M.inactiveTag,children:C("entity.inactive",{defaultValue:"Inactive"})})]})}),w?(0,n.jsx)(c.Draggable,{info:w(e),children:a}):a),isLeaf:!0}};if(!a||0===a.length||!x)return[{key:"root",title:(0,n.jsxs)("span",{className:M.groupNode,children:[(0,n.jsx)(c.Icon,{value:"folder"}),(0,n.jsx)("span",{children:h??C("entity.list.all",{defaultValue:"All"})}),(0,n.jsxs)("span",{className:M.groupCount,children:["(",t.length,")"]})]}),selectable:!1,expanded:!0,children:t.map(e)}];let l={};for(let e of a)l[e.id]=[];let r=[],i=new Set(a.map(e=>e.id));for(let e of t){let t=x(e,a);null!=t&&i.has(t)?l[t].push(e):r.push(e)}let d=a.filter(e=>(l[e.id]??[]).length>0).map(t=>({key:`group-${t.id}`,title:k(t,(l[t.id]??[]).length),selectable:!1,children:(l[t.id]??[]).map(e)}));return r.length>0&&d.push({key:"group-unknown",title:k({id:null,name:C("entity.group.unknown",{defaultValue:"unbekannt"})},r.length),selectable:!1,children:r.map(e)}),d},[t,a,x,C,S,b,f]),A=r().useMemo(()=>{let e=[];for(let t of D)("root"===t.key||"group-unknown"===t.key)&&e.push(t.key);return e},[D]),[z,I]=r().useState(A),T=r().useRef(!1);return r().useEffect(()=>{T.current||I(A)},[A]),(0,n.jsx)(c.ContentLayout,{renderToolbar:(0,n.jsxs)(c.Toolbar,{children:[(0,n.jsx)(c.IconButton,{icon:{value:"refresh"},onClick:v,children:C("toolbar.reload",{defaultValue:"Reload"})}),(0,n.jsx)(c.Dropdown,{menu:{items:[{key:"add",label:g??C("toolbar.new",{defaultValue:"New"}),icon:(0,n.jsx)(c.Icon,{value:"new"}),onClick:()=>y()}]},trigger:["click"],children:(0,n.jsx)(c.DropdownButton,{children:C("toolbar.new",{defaultValue:"New"})})})]}),children:l?(0,n.jsx)("div",{className:M.contentPadding,children:(0,n.jsx)(d.A,{active:!0,title:!1,paragraph:{rows:8}})}):(0,n.jsx)(s.A,{className:M.tree,showLine:!1,defaultExpandedKeys:z,expandedKeys:z,onExpand:e=>{T.current=!0,I(e)},selectable:!0,treeData:D,onSelect:e=>{let t=Array.isArray(e)?e[0]:e;"number"==typeof t&&j(t)}})})}},199:function(e,t,a){a.d(t,{r:()=>r});var n=a(5120);a(7185);var l=a(2696);let r=e=>{let{accept:t,onDrop:a,className:r,disabled:i,isValidData:o,children:d}=e,s=Array.isArray(t)?t:[t];return(0,n.jsx)(l.Droppable,{className:r,disabled:i,isValidContext:function(e){return s.includes(e.type)},isValidData:e=>!o||o(e),onDrop:a,children:d})}},2989:function(e,t,a){a.d(t,{ZF:()=>i,u1:()=>r});var n=a(7185),l=a.n(n);a(2703);let r=function(e){let t=arguments.length>1&&void 0!==arguments[1]?arguments[1]:"An error occurred";if(e instanceof Error)return e.message;if("string"==typeof e)return e;if(e&&"object"==typeof e){if("string"==typeof e.message)return e.message;if("string"==typeof e.detail)return e.detail;if("string"==typeof e.error)return e.error}return t},i=e=>"string"==typeof e&&e.includes("\n")?l().createElement("div",null,...e.split("\n").map((e,t)=>l().createElement("div",{key:t},e))):e},4633:function(e,t,a){a.r(t),a.d(t,{default:()=>H});var n=a(2977),l=a(4781),r=a(4287),i=a(5120),o=a(7185),d=a.n(o),s=a(199),c=a(403);let u=e=>{let{loadOptions:t,getCachedOptions:a,droppableAccept:n,...l}=e,r="multiple"===l.mode,o=r?(0,i.jsx)(c.L,{...l,loadOptions:t,getCachedOptions:a}):(0,i.jsx)(c.I,{...l,loadOptions:t,getCachedOptions:a});return n?(0,i.jsx)(s.r,{accept:n,isValidData:e=>{var t;return"number"==typeof(null==e||null==(t=e.data)?void 0:t.id)},onDrop:e=>{var t;if(l.onChange&&(null==e||null==(t=e.data)?void 0:t.id))if(r){let t=l.value||[],a=Array.isArray(t)?[...t,e.data.id]:[e.data.id];l.onChange(a,{target:{value:a}})}else l.onChange(e.data.id,{target:{value:e.data.id}})},children:o}):o},p=e=>(0,i.jsxs)("svg",{xmlns:"http://www.w3.org/2000/svg",xmlSpace:"preserve",viewBox:"0 0 48 48",width:"1em",height:"1em",...e,children:[(0,i.jsx)("path",{fill:"#CFD8DC",d:"M5 19h38v19H5z"}),(0,i.jsx)("path",{fill:"#B0BEC5",d:"M5 38h38v4H5z"}),(0,i.jsx)("path",{fill:"#455A64",d:"M27 24h12v18H27z"}),(0,i.jsx)("path",{fill:"#E3F2FD",d:"M9 24h14v11H9z"}),(0,i.jsx)("path",{fill:"#1E88E5",d:"M10 25h12v9H10z"}),(0,i.jsx)("path",{fill:"#90A4AE",d:"M36.5 33.5c-.3 0-.5.2-.5.5v2c0 .3.2.5.5.5s.5-.2.5-.5v-2c0-.3-.2-.5-.5-.5"}),(0,i.jsxs)("g",{fill:"#558B2F",children:[(0,i.jsx)("circle",{cx:24,cy:19,r:3}),(0,i.jsx)("circle",{cx:36,cy:19,r:3}),(0,i.jsx)("circle",{cx:12,cy:19,r:3})]}),(0,i.jsx)("path",{fill:"#7CB342",d:"M40 6H8c-1.1 0-2 .9-2 2v3h36V8c0-1.1-.9-2-2-2M21 11h6v8h-6zM37 11h-5l1 8h6zM11 11h5l-1 8H9z"}),(0,i.jsxs)("g",{fill:"#FFA000",children:[(0,i.jsx)("circle",{cx:30,cy:19,r:3}),(0,i.jsx)("path",{d:"M45 19c0 1.7-1.3 3-3 3s-3-1.3-3-3 1.3-3 3-3z"}),(0,i.jsx)("circle",{cx:18,cy:19,r:3}),(0,i.jsx)("path",{d:"M3 19c0 1.7 1.3 3 3 3s3-1.3 3-3-1.3-3-3-3z"})]}),(0,i.jsx)("g",{fill:"#FFC107",children:(0,i.jsx)("path",{d:"M32 11h-5v8h6zM42 11h-5l2 8h6zM16 11h5v8h-6zM6 11h5l-2 8H3z"})})]}),h={onInit(){let e=n.container.get(l.serviceIds.iconLibrary);e.register({name:"coreshop_store",component:p}),e.register({name:"coreshop_nav_icon_store",component:p}),e.register({name:"coreshop_stores",component:p})}};var g=a(5599);a(8268),a(9646);var f=a(7350),x=a(2696),v=a(2989),y=a(6147);let m=e=>{let{dirty:t,loading:a,onReload:n,onSave:l,leftExtras:r}=e,{t:o}=(0,y.useTranslation)();return(0,i.jsxs)(x.Toolbar,{children:[t?(0,i.jsx)(x.Popconfirm,{title:o("toolbar.reload.confirmation",{defaultValue:"Discard changes and reload?"}),onConfirm:n,children:(0,i.jsx)(x.IconButton,{icon:{value:"refresh"},children:o("toolbar.reload",{defaultValue:"Reload"})})}):(0,i.jsx)(x.IconButton,{icon:{value:"refresh"},onClick:n,children:o("toolbar.reload",{defaultValue:"Reload"})}),r,(0,i.jsx)(x.Button,{disabled:!t||a,loading:a,onClick:l,type:"primary",children:o("toolbar.save-and-publish",{defaultValue:"Save & Publish"})})]})};var j=a(2703);let b=d().createContext(void 0),w=e=>{let t,{children:a}=e,n=(t=(0,j.useSettings)(),(0,o.useMemo)(()=>{let e=Array.isArray(null==t?void 0:t.validLanguages)?t.validLanguages:[];return e.length>0?e:["en"]},[null==t?void 0:t.validLanguages])),[l,r]=d().useState(n[0]??"en");d().useEffect(()=>{!n.includes(l)&&n.length>0&&r(n[0])},[n]);let s=d().useMemo(()=>({locales:n,currentLocale:l,setCurrentLocale:r}),[n,l]);return(0,i.jsx)(b.Provider,{value:s,children:a})},S=()=>{let e=d().useContext(b);if(!e)throw Error("useLocalization must be used within LocalizationProvider");return e};var C=a(5017),M=a(2056);let k=(0,a(9).rU)(e=>{let{token:t,css:a}=e;return{contentPadding:a`
    padding: ${t.paddingSM}px;
  `,detailContent:a`
    overflow: auto;
  `}});function D(e,t){let a={...e};for(let n of Object.keys(t)){let l=e[n],r=t[n];null===r||"object"!=typeof r||Array.isArray(r)||null===l||"object"!=typeof l||Array.isArray(l)?a[n]=r:a[n]=D(l,r)}return a}function A(e){let{api:t,getTitle:a,buildSavePayload:n,renderLeft:l,renderDetail:r,leftExtras:o,localizable:s}=e,{styles:c}=k(),{list:u,loadingList:p,loadList:h,tabs:g,activeKey:f,setActiveKey:y,activeTab:j,findTab:b,updateTab:S,openTab:M,onSave:A,onReload:T,onDelete:$,forceCloseTab:F}=function(e){var t;let{api:a,getTitle:n,buildSavePayload:l}=e,r=(0,x.useMessage)(),[i,o]=d().useState([]),[s,c]=d().useState([]),[u,p]=d().useState(void 0),[h,g]=d().useState(!1),f=(e,t)=>void 0!==n?n(e,t):(null==t?void 0:t.name)??(null==e?void 0:e.name)??`#${(null==e?void 0:e.id)??""}`,y=async()=>{g(!0);try{let e=await a.list();o(e),c(t=>t.map(t=>{let a=e.find(e=>e.id===t.id);return{...t,title:f(a,t.data)}}))}catch(e){r.error((0,v.ZF)((0,v.u1)(e,"Failed to load list")))}finally{g(!1)}};d().useEffect(()=>{y()},[]);let m=e=>s.find(t=>t.id===e),j=(e,t)=>{c(a=>a.map(a=>{if(a.id!==e)return a;let n="function"==typeof t?t(a):t;return{...a,...n}}))},b=async e=>{c(t=>t.some(t=>t.id===e)?t:[...t,{id:e,title:f(i.find(t=>t.id===e)),dirty:!1,loading:!1}]),p(String(e)),await S(e)},w=e=>{c(t=>t.filter(t=>t.id!==e)),u===String(e)&&setTimeout(()=>{p(t=>{let a=s.filter(t=>t.id!==e);return a.length?String(a[a.length-1].id):void 0})},0)},S=async e=>{j(e,{loading:!0});try{let t=await a.get(e),n=i.find(t=>t.id===e);j(e,{data:t.data,dirty:!1,loading:!1,title:f(n,t.data)})}catch(t){r.error((0,v.ZF)((0,v.u1)(t,"Failed to load"))),j(e,{loading:!1})}},C=async e=>{let t=m(e);if((null==t?void 0:t.data)==null)return;j(e,{loading:!0});let n=void 0!==l?l(t.data):t.data;try{await a.save(n),j(e,{dirty:!1}),await y(),r.success("Saved successfully")}catch(e){r.error((0,v.ZF)((0,v.u1)(e,"Failed to save")))}finally{j(e,{loading:!1})}},M=async e=>{await S(e)},k=async e=>{try{await a.delete(e),await y(),w(e),r.success("Deleted successfully")}catch(e){r.error((0,v.ZF)((0,v.u1)(e,"Failed to delete")))}},D=void 0!==u?parseInt(u):null==(t=s[0])?void 0:t.id,A=void 0!==D?s.find(e=>e.id===D):void 0;return{list:i,loadingList:h,loadList:y,tabs:s,activeKey:u,setActiveKey:p,activeTab:A,findTab:m,updateTab:j,openTab:b,loadDetail:S,forceCloseTab:w,onSave:C,onReload:M,onDelete:k}}({api:t,getTitle:a,buildSavePayload:n}),[N,V]=d().useState(null),E={id:"entity-list",size:25,minSize:220,children:[l({items:u,loading:p,loadList:h,openTab:M,onDelete:$})]},_={id:"entity-detail-tabs",size:75,minSize:400,children:[(0,i.jsxs)(x.ContentLayout,{renderToolbar:s?(0,i.jsx)(z,{dirty:null==j?void 0:j.dirty,loading:null==j?void 0:j.loading,onReload:()=>{j&&T(j.id)},onSave:()=>{j&&A(j.id)},leftExtras:o}):(0,i.jsx)(m,{dirty:null==j?void 0:j.dirty,loading:null==j?void 0:j.loading,onReload:()=>{j&&T(j.id)},onSave:()=>{j&&A(j.id)},leftExtras:o}),children:[(0,i.jsx)(x.Tabs,{activeKey:f,items:g.map(e=>({key:String(e.id),label:(0,i.jsx)(x.Popconfirm,{onCancel:()=>{V(null)},onConfirm:()=>{F(e.id),V(null)},open:N===e.id,title:"Discard changes and close?",children:`${e.title}${e.dirty?" *":""}`})})),onChange:e=>y(e),onClose:e=>{let t,a;(a=b(t=parseInt(e)))&&(a.dirty?V(t):F(t))}}),(0,i.jsx)(x.Content,{className:`detail-tabs__content ${c.detailContent}`,children:void 0!==j&&(j.loading?(0,i.jsx)("div",{className:c.contentPadding,children:(0,i.jsx)(C.A,{active:!0,paragraph:{rows:10}})}):s?(0,i.jsx)(I,{render:e=>r(j.data,e=>{j&&S(j.id,t=>({data:D(t.data,e),dirty:!0}))},e)}):r(j.data,e=>{j&&S(j.id,t=>({data:D(t.data,e),dirty:!0}))}))})]},"tabs-layout")]},P=(0,i.jsx)(x.SplitLayout,{leftItem:E,rightItem:_,withDivider:!0,withToolbar:!0});return s?(0,i.jsx)(w,{children:P}):P}function z(e){let{dirty:t,loading:a,onReload:n,onSave:l,leftExtras:r}=e,{locales:o,currentLocale:d,setCurrentLocale:s}=S();return(0,i.jsx)(m,{dirty:t,loading:a,onReload:n,onSave:l,leftExtras:(0,i.jsxs)(i.Fragment,{children:[(0,i.jsx)(M.A,{size:"small",value:d,options:o.map(e=>({value:e,label:e.toUpperCase()})),onChange:s,style:{marginRight:8}}),r]})})}let I=e=>{let{render:t}=e,{locales:a,currentLocale:n}=S();return(0,i.jsx)(i.Fragment,{children:t({locales:a,currentLocale:n})})};function T(e){let{api:t,getTitle:a,buildSavePayload:n,onAdd:l,renderDetail:r,leftExtras:o,localizable:s,buildDragInfo:c,dragType:u,leftRootTitle:p,leafIcon:h}=e,g=d().useMemo(()=>c||(u?e=>({type:u,title:(null==e?void 0:e.name)??`#${null==e?void 0:e.id}`,icon:{value:"widget-default"},data:e}):void 0),[c,u]);return(0,i.jsx)(A,{api:t,getTitle:a,buildSavePayload:n,localizable:s,leftExtras:o,renderLeft:e=>{let{items:t,loading:a,loadList:n,openTab:r,onDelete:o}=e;return(0,i.jsx)(f.u,{items:t,loading:a,rootTitle:p,leafIcon:h,buildDragInfo:g,onAdd:async()=>{if(!l)return;let e=await l();await n(),await r(e)},onDelete:e=>{o(e)},onReload:()=>{n()},onSelect:e=>{r(e)}})},renderDetail:(e,t,a)=>r(e,t,a)})}a(9289),a(5579),a(6905),a(8385),a(3508);let $=new g.G({basePath:"/pimcore-studio/api",resourcePath:"/coreshop/stores"});var F=a(3869);let N=e=>(0,i.jsx)("div",{style:{padding:12},children:(0,i.jsx)(F.q,{...e,blockPrefix:"coreshop_store"})}),V=()=>{let{t:e}=(0,y.useTranslation)(),t=(0,x.useFormModal)();return(0,i.jsx)(T,{api:$,dragType:"coreshop:store",leftRootTitle:e("coreshop_stores",{defaultValue:"Stores"}),getTitle:(e,t)=>(null==t?void 0:t.name)??(null==e?void 0:e.name)??`Store #${(null==t?void 0:t.id)??(null==e?void 0:e.id)??""}`,buildSavePayload:e=>e,onAdd:async()=>await new Promise(a=>{t.input({title:e("coreshop_store_add",{defaultValue:"Add Store"}),label:e("coreshop_name",{defaultValue:"Name"}),rule:{required:!0,message:e("coreshop_name_required",{defaultValue:"Name is required"})},onOk:async e=>{let t=await $.add({name:e});void 0!==t.data.id&&a(t.data.id)}})}),renderDetail:(e,t)=>(0,i.jsx)(N,{data:e,onChange:t})})},{load:E,getCache:_,clearCache:P}=(0,a(4096).E)(async()=>(await $.list()).map(e=>({value:e.id,label:e.name})));var O=a(4264);class R extends O.O{constructor(...e){super(...e),this.id="coreShopStore",this.loadOptions=E,this.getCachedOptions=_}}var L=a(4893);class B extends L.t{constructor(...e){super(...e),this.id="coreShopStoreMultiselect",this.loadOptions=E,this.getCachedOptions=_}}let H={name:"coreshop-store",onInit(){let e=n.container.get(l.serviceIds["DynamicTypes/ObjectDataRegistry"]);e.registerDynamicType(new R),e.registerDynamicType(new B),n.container.get(r.widgetRegistryServiceId).register("coreshop_store_choice",e=>({component:u,props:{loadOptions:E,getCachedOptions:_,droppableAccept:"coreshop:store",mode:e.multiple?"multiple":void 0}}))},onStartup(e){let{moduleSystem:t}=e;t.registerModule(h),n.container.get(l.serviceIds.widgetManager).registerWidget({name:"coreshop-store-store",component:V})}}},3869:function(e,t,a){a.d(t,{$J:()=>r.$,Dq:()=>l.D,Sx:()=>o.Sx,XH:()=>o.XH,Y8:()=>n.Y,ir:()=>o.ir,q:()=>i.q});var n=a(9986),l=a(4929),r=a(9035),i=a(4296),o=a(7281)}}]);