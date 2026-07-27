/*! For license information please see __federation_expose_default_export.067f1c65.js.LICENSE.txt */
"use strict";(self["chunk_coreshopstore "]=self["chunk_coreshopstore "]||[]).push([["525"],{7350:function(e,t,a){a.d(t,{u:()=>h});var n=a(2855),l=a(5168),r=a.n(l),i=a(5210),o=a(8421),d=a(5017),s=a(2162),c=a(2696),u=a(199);let p=(0,a(9).rU)(e=>{let{token:t,css:a}=e;return{tree:a`
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
  `}}),h=e=>{let{items:t,groups:a,loading:l,rootTitle:h,addLabel:g,leafIcon:f="widget-default",resolveGroupId:v,onReload:x,onAdd:y,onDelete:m,onSelect:j,onMove:b,buildDragInfo:w,dragType:S}=e,{t:C}=(0,i.useTranslation)(),{styles:D}=p(),M=(e,t)=>{let a=(0,n.jsxs)("span",{className:D.groupNode,children:[(0,n.jsx)(c.Icon,{value:"folder"}),(0,n.jsx)("span",{children:e.name}),(0,n.jsxs)("span",{className:D.groupCount,children:["(",t,")"]})]});return S&&b?(0,n.jsx)(u.r,{className:D.droppableInline,accept:S,isValidData:e=>{var t;return"number"==typeof(null==e||null==(t=e.data)?void 0:t.id)},onDrop:t=>{var a;let n=null==t||null==(a=t.data)?void 0:a.id;"number"==typeof n&&b(n,null===e.id?null:e.id)},children:a}):a},k=r().useMemo(()=>{let e=e=>{let t,a;return{key:e.id,title:(t=!1===e.active,a=(0,n.jsx)(c.Dropdown,{trigger:["contextMenu"],menu:{items:[{key:"delete",icon:(0,n.jsx)(c.Icon,{value:"trash"}),label:C("toolbar.delete",{defaultValue:"Delete"}),onClick:()=>m(e.id)}]},children:(0,n.jsxs)("span",{className:`${D.leafNode} ${t?D.inactive:""}`,children:[(0,n.jsx)("span",{className:D.leafIcon,children:(0,n.jsx)(c.Icon,{value:f})}),e.name,t&&(0,n.jsx)(o.A,{className:D.inactiveTag,children:C("entity.inactive",{defaultValue:"Inactive"})})]})}),w?(0,n.jsx)(c.Draggable,{info:w(e),children:a}):a),isLeaf:!0}};if(!a||0===a.length||!v)return[{key:"root",title:(0,n.jsxs)("span",{className:D.groupNode,children:[(0,n.jsx)(c.Icon,{value:"folder"}),(0,n.jsx)("span",{children:h??C("entity.list.all",{defaultValue:"All"})}),(0,n.jsxs)("span",{className:D.groupCount,children:["(",t.length,")"]})]}),selectable:!1,expanded:!0,children:t.map(e)}];let l={};for(let e of a)l[e.id]=[];let r=[],i=new Set(a.map(e=>e.id));for(let e of t){let t=v(e,a);null!=t&&i.has(t)?l[t].push(e):r.push(e)}let d=a.filter(e=>(l[e.id]??[]).length>0).map(t=>({key:`group-${t.id}`,title:M(t,(l[t.id]??[]).length),selectable:!1,children:(l[t.id]??[]).map(e)}));return r.length>0&&d.push({key:"group-unknown",title:M({id:null,name:C("entity.group.unknown",{defaultValue:"unbekannt"})},r.length),selectable:!1,children:r.map(e)}),d},[t,a,v,C,S,b,f]),A=r().useMemo(()=>{let e=[];for(let t of k)("root"===t.key||"group-unknown"===t.key)&&e.push(t.key);return e},[k]),[I,T]=r().useState(A),z=r().useRef(!1);return r().useEffect(()=>{z.current||T(A)},[A]),(0,n.jsx)(c.ContentLayout,{renderToolbar:(0,n.jsxs)(c.Toolbar,{children:[(0,n.jsx)(c.IconButton,{icon:{value:"refresh"},onClick:x,children:C("toolbar.reload",{defaultValue:"Reload"})}),(0,n.jsx)(c.Dropdown,{menu:{items:[{key:"add",label:g??C("toolbar.new",{defaultValue:"New"}),icon:(0,n.jsx)(c.Icon,{value:"new"}),onClick:()=>y()}]},trigger:["click"],children:(0,n.jsx)(c.DropdownButton,{children:C("toolbar.new",{defaultValue:"New"})})})]}),children:l?(0,n.jsx)("div",{className:D.contentPadding,children:(0,n.jsx)(d.A,{active:!0,title:!1,paragraph:{rows:8}})}):(0,n.jsx)(s.A,{className:D.tree,showLine:!1,defaultExpandedKeys:I,expandedKeys:I,onExpand:e=>{z.current=!0,T(e)},selectable:!0,treeData:k,onSelect:e=>{let t=Array.isArray(e)?e[0]:e;"number"==typeof t&&j(t)}})})}},199:function(e,t,a){a.d(t,{r:()=>r});var n=a(2855);a(5168);var l=a(2696);let r=e=>{let{accept:t,onDrop:a,className:r,disabled:i,isValidData:o,children:d}=e,s=Array.isArray(t)?t:[t];return(0,n.jsx)(l.Droppable,{className:r,disabled:i,isValidContext:function(e){return s.includes(e.type)},isValidData:e=>!o||o(e),onDrop:a,children:d})}},2989:function(e,t,a){a.d(t,{ZF:()=>i,u1:()=>r});var n=a(5168),l=a.n(n);a(2703);let r=function(e){let t=arguments.length>1&&void 0!==arguments[1]?arguments[1]:"An error occurred";if(e instanceof Error)return e.message;if("string"==typeof e)return e;if(e&&"object"==typeof e){if("string"==typeof e.message)return e.message;if("string"==typeof e.detail)return e.detail;if("string"==typeof e.error)return e.error}return t},i=e=>"string"==typeof e&&e.includes("\n")?l().createElement("div",null,...e.split("\n").map((e,t)=>l().createElement("div",{key:t},e))):e},6267:function(e,t,a){a.r(t),a.d(t,{default:()=>K});var n=a(2977),l=a(4781),r=a(5782),i=a(2855),o=a(5168),d=a.n(o),s=a(199),c=a(403);let u=e=>{let{loadOptions:t,getCachedOptions:a,droppableAccept:n,...l}=e,r="multiple"===l.mode,o=r?(0,i.jsx)(c.L,{...l,loadOptions:t,getCachedOptions:a}):(0,i.jsx)(c.I,{...l,loadOptions:t,getCachedOptions:a});return n?(0,i.jsx)(s.r,{accept:n,isValidData:e=>{var t;return"number"==typeof(null==e||null==(t=e.data)?void 0:t.id)},onDrop:e=>{var t;if(l.onChange&&(null==e||null==(t=e.data)?void 0:t.id))if(r){let t=l.value||[],a=Array.isArray(t)?[...t,e.data.id]:[e.data.id];l.onChange(a,{target:{value:a}})}else l.onChange(e.data.id,{target:{value:e.data.id}})},children:o}):o};var p=a(2056),h=a(3842);let g=e=>{let{value:t,onChange:a,store:n,width:l}=e;return(0,i.jsx)(p.A,{allowClear:!0,getPopupContainer:e=>(null==e?void 0:e.parentElement)??document.body,onChange:e=>null==a?void 0:a(e??null),optionFilterProp:"label",options:(n??[]).map(e=>Array.isArray(e)?{value:String(e[0]),label:String(e[1])}:{value:String(e),label:String(e)}),popupMatchSelectWidth:!1,showSearch:!0,style:{width:l??"100%",minWidth:200},value:null!=t&&""!==t?String(t):void 0})};class f extends h.DynamicTypeDocumentEditableAbstract{getEditableDataComponent(e){var t,a;return(0,i.jsx)(g,{onChange:e.onChange,store:null==(t=e.config)?void 0:t.store,value:e.value,width:null==(a=e.config)?void 0:a.width})}isEmpty(e){return null==e||""===e}constructor(e){super(),this.id=e}}let v=e=>(0,i.jsxs)("svg",{xmlns:"http://www.w3.org/2000/svg",xmlSpace:"preserve",viewBox:"0 0 48 48",width:"1em",height:"1em",...e,children:[(0,i.jsx)("path",{fill:"#CFD8DC",d:"M5 19h38v19H5z"}),(0,i.jsx)("path",{fill:"#B0BEC5",d:"M5 38h38v4H5z"}),(0,i.jsx)("path",{fill:"#455A64",d:"M27 24h12v18H27z"}),(0,i.jsx)("path",{fill:"#E3F2FD",d:"M9 24h14v11H9z"}),(0,i.jsx)("path",{fill:"#1E88E5",d:"M10 25h12v9H10z"}),(0,i.jsx)("path",{fill:"#90A4AE",d:"M36.5 33.5c-.3 0-.5.2-.5.5v2c0 .3.2.5.5.5s.5-.2.5-.5v-2c0-.3-.2-.5-.5-.5"}),(0,i.jsxs)("g",{fill:"#558B2F",children:[(0,i.jsx)("circle",{cx:24,cy:19,r:3}),(0,i.jsx)("circle",{cx:36,cy:19,r:3}),(0,i.jsx)("circle",{cx:12,cy:19,r:3})]}),(0,i.jsx)("path",{fill:"#7CB342",d:"M40 6H8c-1.1 0-2 .9-2 2v3h36V8c0-1.1-.9-2-2-2M21 11h6v8h-6zM37 11h-5l1 8h6zM11 11h5l-1 8H9z"}),(0,i.jsxs)("g",{fill:"#FFA000",children:[(0,i.jsx)("circle",{cx:30,cy:19,r:3}),(0,i.jsx)("path",{d:"M45 19c0 1.7-1.3 3-3 3s-3-1.3-3-3 1.3-3 3-3z"}),(0,i.jsx)("circle",{cx:18,cy:19,r:3}),(0,i.jsx)("path",{d:"M3 19c0 1.7 1.3 3 3 3s3-1.3 3-3-1.3-3-3-3z"})]}),(0,i.jsx)("g",{fill:"#FFC107",children:(0,i.jsx)("path",{d:"M32 11h-5v8h6zM42 11h-5l2 8h6zM16 11h5v8h-6zM6 11h5l-2 8H3z"})})]}),x={onInit(){let e=n.container.get(l.serviceIds.iconLibrary);e.register({name:"coreshop_store",component:v}),e.register({name:"coreshop_nav_icon_store",component:v}),e.register({name:"coreshop_stores",component:v})}};var y=a(5599);a(8268),a(9646);var m=a(7350),j=a(2696),b=a(2989),w=a(5210);let S=e=>{let{dirty:t,loading:a,onReload:n,onSave:l,leftExtras:r}=e,{t:o}=(0,w.useTranslation)();return(0,i.jsxs)(j.Toolbar,{children:[t?(0,i.jsx)(j.Popconfirm,{title:o("toolbar.reload.confirmation",{defaultValue:"Discard changes and reload?"}),onConfirm:n,children:(0,i.jsx)(j.IconButton,{icon:{value:"refresh"},children:o("toolbar.reload",{defaultValue:"Reload"})})}):(0,i.jsx)(j.IconButton,{icon:{value:"refresh"},onClick:n,children:o("toolbar.reload",{defaultValue:"Reload"})}),r,(0,i.jsx)(j.Button,{disabled:!t||a,loading:a,onClick:l,type:"primary",children:o("toolbar.save-and-publish",{defaultValue:"Save & Publish"})})]})};var C=a(2703);let D=d().createContext(void 0),M=e=>{let t,{children:a}=e,n=(t=(0,C.useSettings)(),(0,o.useMemo)(()=>{let e=Array.isArray(null==t?void 0:t.validLanguages)?t.validLanguages:[];return e.length>0?e:["en"]},[null==t?void 0:t.validLanguages])),[l,r]=d().useState(n[0]??"en");d().useEffect(()=>{!n.includes(l)&&n.length>0&&r(n[0])},[n]);let s=d().useMemo(()=>({locales:n,currentLocale:l,setCurrentLocale:r}),[n,l]);return(0,i.jsx)(D.Provider,{value:s,children:a})},k=()=>{let e=d().useContext(D);if(!e)throw Error("useLocalization must be used within LocalizationProvider");return e};var A=a(5017);let I=(0,a(9).rU)(e=>{let{token:t,css:a}=e;return{contentPadding:a`
    padding: ${t.paddingSM}px;
  `,detailContent:a`
    overflow: auto;
  `}});function T(e,t){let a={...e};for(let n of Object.keys(t)){let l=e[n],r=t[n];null===r||"object"!=typeof r||Array.isArray(r)||null===l||"object"!=typeof l||Array.isArray(l)?a[n]=r:a[n]=T(l,r)}return a}function z(e){let{api:t,getTitle:a,buildSavePayload:n,renderLeft:l,renderDetail:r,leftExtras:o,localizable:s}=e,{styles:c}=I(),{list:u,loadingList:p,loadList:h,tabs:g,activeKey:f,setActiveKey:v,activeTab:x,findTab:y,updateTab:m,openTab:w,onSave:C,onReload:D,onDelete:k,forceCloseTab:z}=function(e){var t;let{api:a,getTitle:n,buildSavePayload:l}=e,r=(0,j.useMessage)(),[i,o]=d().useState([]),[s,c]=d().useState([]),[u,p]=d().useState(void 0),[h,g]=d().useState(!1),f=(e,t)=>void 0!==n?n(e,t):(null==t?void 0:t.name)??(null==e?void 0:e.name)??`#${(null==e?void 0:e.id)??""}`,v=async()=>{g(!0);try{let e=await a.list();o(e),c(t=>t.map(t=>{let a=e.find(e=>e.id===t.id);return{...t,title:f(a,t.data)}}))}catch(e){r.error((0,b.ZF)((0,b.u1)(e,"Failed to load list")))}finally{g(!1)}};d().useEffect(()=>{v()},[]);let x=e=>s.find(t=>t.id===e),y=(e,t)=>{c(a=>a.map(a=>{if(a.id!==e)return a;let n="function"==typeof t?t(a):t;return{...a,...n}}))},m=async e=>{c(t=>t.some(t=>t.id===e)?t:[...t,{id:e,title:f(i.find(t=>t.id===e)),dirty:!1,loading:!1}]),p(String(e)),await S(e)},w=e=>{c(t=>t.filter(t=>t.id!==e)),u===String(e)&&setTimeout(()=>{p(t=>{let a=s.filter(t=>t.id!==e);return a.length?String(a[a.length-1].id):void 0})},0)},S=async e=>{y(e,{loading:!0});try{let t=await a.get(e),n=i.find(t=>t.id===e);y(e,{data:t.data,dirty:!1,loading:!1,title:f(n,t.data)})}catch(t){r.error((0,b.ZF)((0,b.u1)(t,"Failed to load"))),y(e,{loading:!1})}},C=async e=>{let t=x(e);if((null==t?void 0:t.data)==null)return;y(e,{loading:!0});let n=void 0!==l?l(t.data):t.data;try{await a.save(n),y(e,{dirty:!1}),await v(),r.success("Saved successfully")}catch(e){r.error((0,b.ZF)((0,b.u1)(e,"Failed to save")))}finally{y(e,{loading:!1})}},D=async e=>{await S(e)},M=async e=>{try{await a.delete(e),await v(),w(e),r.success("Deleted successfully")}catch(e){r.error((0,b.ZF)((0,b.u1)(e,"Failed to delete")))}},k=void 0!==u?parseInt(u):null==(t=s[0])?void 0:t.id,A=void 0!==k?s.find(e=>e.id===k):void 0;return{list:i,loadingList:h,loadList:v,tabs:s,activeKey:u,setActiveKey:p,activeTab:A,findTab:x,updateTab:y,openTab:m,loadDetail:S,forceCloseTab:w,onSave:C,onReload:D,onDelete:M}}({api:t,getTitle:a,buildSavePayload:n}),[F,N]=d().useState(null),V={id:"entity-list",size:25,minSize:220,children:[l({items:u,loading:p,loadList:h,openTab:w,onDelete:k})]},_={id:"entity-detail-tabs",size:75,minSize:400,children:[(0,i.jsxs)(j.ContentLayout,{renderToolbar:s?(0,i.jsx)(E,{dirty:null==x?void 0:x.dirty,loading:null==x?void 0:x.loading,onReload:()=>{x&&D(x.id)},onSave:()=>{x&&C(x.id)},leftExtras:o}):(0,i.jsx)(S,{dirty:null==x?void 0:x.dirty,loading:null==x?void 0:x.loading,onReload:()=>{x&&D(x.id)},onSave:()=>{x&&C(x.id)},leftExtras:o}),children:[(0,i.jsx)(j.Tabs,{activeKey:f,items:g.map(e=>({key:String(e.id),label:(0,i.jsx)(j.Popconfirm,{onCancel:()=>{N(null)},onConfirm:()=>{z(e.id),N(null)},open:F===e.id,title:"Discard changes and close?",children:`${e.title}${e.dirty?" *":""}`})})),onChange:e=>v(e),onClose:e=>{let t,a;(a=y(t=parseInt(e)))&&(a.dirty?N(t):z(t))}}),(0,i.jsx)(j.Content,{className:`detail-tabs__content ${c.detailContent}`,children:void 0!==x&&(x.loading?(0,i.jsx)("div",{className:c.contentPadding,children:(0,i.jsx)(A.A,{active:!0,paragraph:{rows:10}})}):s?(0,i.jsx)($,{render:e=>r(x.data,e=>{x&&m(x.id,t=>({data:T(t.data,e),dirty:!0}))},e)}):r(x.data,e=>{x&&m(x.id,t=>({data:T(t.data,e),dirty:!0}))}))})]},"tabs-layout")]},P=(0,i.jsx)(j.SplitLayout,{leftItem:V,rightItem:_,withDivider:!0,withToolbar:!0});return s?(0,i.jsx)(M,{children:P}):P}function E(e){let{dirty:t,loading:a,onReload:n,onSave:l,leftExtras:r}=e,{locales:o,currentLocale:d,setCurrentLocale:s}=k();return(0,i.jsx)(S,{dirty:t,loading:a,onReload:n,onSave:l,leftExtras:(0,i.jsxs)(i.Fragment,{children:[(0,i.jsx)(p.A,{size:"small",value:d,options:o.map(e=>({value:e,label:e.toUpperCase()})),onChange:s,style:{marginRight:8}}),r]})})}let $=e=>{let{render:t}=e,{locales:a,currentLocale:n}=k();return(0,i.jsx)(i.Fragment,{children:t({locales:a,currentLocale:n})})};function F(e){let{api:t,getTitle:a,buildSavePayload:n,onAdd:l,renderDetail:r,leftExtras:o,localizable:s,buildDragInfo:c,dragType:u,leftRootTitle:p,leafIcon:h}=e,g=d().useMemo(()=>c||(u?e=>({type:u,title:(null==e?void 0:e.name)??`#${null==e?void 0:e.id}`,icon:{value:"widget-default"},data:e}):void 0),[c,u]);return(0,i.jsx)(z,{api:t,getTitle:a,buildSavePayload:n,localizable:s,leftExtras:o,renderLeft:e=>{let{items:t,loading:a,loadList:n,openTab:r,onDelete:o}=e;return(0,i.jsx)(m.u,{items:t,loading:a,rootTitle:p,leafIcon:h,buildDragInfo:g,onAdd:async()=>{if(!l)return;let e=await l();await n(),await r(e)},onDelete:e=>{o(e)},onReload:()=>{n()},onSelect:e=>{r(e)}})},renderDetail:(e,t,a)=>r(e,t,a)})}a(9289),a(5579),a(6905),a(8385),a(3508);let N=new y.G({basePath:"/pimcore-studio/api",resourcePath:"/coreshop/stores"});var V=a(3869);let _=e=>(0,i.jsx)("div",{style:{padding:12},children:(0,i.jsx)(V.q,{...e,blockPrefix:"coreshop_store"})}),P=()=>{let{t:e}=(0,w.useTranslation)(),t=(0,j.useFormModal)();return(0,i.jsx)(F,{api:N,dragType:"coreshop:store",leftRootTitle:e("coreshop_stores",{defaultValue:"Stores"}),getTitle:(e,t)=>(null==t?void 0:t.name)??(null==e?void 0:e.name)??`Store #${(null==t?void 0:t.id)??(null==e?void 0:e.id)??""}`,buildSavePayload:e=>e,onAdd:async()=>await new Promise(a=>{t.input({title:e("coreshop_store_add",{defaultValue:"Add Store"}),label:e("coreshop_name",{defaultValue:"Name"}),rule:{required:!0,message:e("coreshop_name_required",{defaultValue:"Name is required"})},onOk:async e=>{let t=await N.add({name:e});void 0!==t.data.id&&a(t.data.id)}})}),renderDetail:(e,t)=>(0,i.jsx)(_,{data:e,onChange:t})})},{load:R,getCache:O,clearCache:L}=(0,a(4096).E)(async()=>(await N.list()).map(e=>({value:e.id,label:e.name})));var B=a(4264);class H extends B.O{constructor(...e){super(...e),this.id="coreShopStore",this.loadOptions=R,this.getCachedOptions=O}}var q=a(4893);class Z extends q.t{constructor(...e){super(...e),this.id="coreShopStoreMultiselect",this.loadOptions=R,this.getCachedOptions=O}}let K={name:"coreshop-store",onInit(){let e=n.container.get(l.serviceIds["DynamicTypes/DocumentEditableRegistry"]);for(let t of["coreshop_store"])e.registerDynamicType(new f(t));try{let e=n.container.get(l.serviceIds["DynamicTypes/ObjectDataRegistry"]);e.registerDynamicType(new H),e.registerDynamicType(new Z),n.container.get(r.widgetRegistryServiceId).register("coreshop_store_choice",e=>({component:u,props:{loadOptions:R,getCachedOptions:O,droppableAccept:"coreshop:store",mode:e.multiple?"multiple":void 0}}))}catch{}},onStartup(e){let{moduleSystem:t}=e;t.registerModule(x);try{n.container.get(l.serviceIds.widgetManager).registerWidget({name:"coreshop-store-store",component:P})}catch{}}}},3869:function(e,t,a){a.d(t,{$J:()=>r.$,Dq:()=>l.D,Sx:()=>o.Sx,XH:()=>o.XH,Y8:()=>n.Y,ir:()=>o.ir,q:()=>i.q});var n=a(9986),l=a(4929),r=a(9035),i=a(4296),o=a(7281)}}]);