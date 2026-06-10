/*! For license information please see __federation_expose_default_export.db0ddc3e.js.LICENSE.txt */
"use strict";(self["chunk_coreshopmessenger "]=self["chunk_coreshopmessenger "]||[]).push([["525"],{3676:function(e,r,s){s.r(r),s.d(r,{default:()=>G});var a=s(2977),t=s(4781),l=s(5120),o=s(7185);let n=e=>(0,l.jsxs)("svg",{xmlns:"http://www.w3.org/2000/svg",viewBox:"0 0 48 48",width:"1em",height:"1em",...e,children:[(0,l.jsx)("path",{fill:"#2196F3",d:"M40 10H8a4 4 0 0 0-4 4v20a4 4 0 0 0 4 4h32a4 4 0 0 0 4-4V14a4 4 0 0 0-4-4"}),(0,l.jsx)("path",{fill:"#0D47A1",d:"M44 14.025a4 4 0 0 0-.24-1.32L24 27.025 4.241 12.705A4 4 0 0 0 4 14.025V15l20 14.495L44 15z"})]}),i={onInit(){let e=a.container.get(t.serviceIds.iconLibrary);e.register({name:"coreshop_messenger",component:n}),e.register({name:"coreshop_nav_icon_messenger",component:n})}};var c=s(1605),d=s(6284),u=s(6096),h=s(4958),m=s(8949),g=s(1925),p=s(323),f=s(6147),x=s(9911),y=s(719),b=s(9700),_=s(2580);let{Text:w}=x.A,v=(0,p.rU)(e=>{let{css:r,token:s}=e;return{container:r`
    background: ${s.colorBgContainer};
    border-radius: ${s.borderRadius}px;
    border: 1px solid ${s.colorBorderSecondary};
    padding: 16px;
    margin-bottom: 16px;
  `,loadingContainer:r`
    display: flex;
    align-items: center;
    justify-content: center;
    height: 180px;
    background: ${s.colorBgContainer};
    border-radius: ${s.borderRadius}px;
    border: 1px solid ${s.colorBorderSecondary};
    margin-bottom: 16px;
  `,summary:r`
    display: flex;
    align-items: center;
    gap: 16px;
    margin-bottom: 16px;
    padding-bottom: 12px;
    border-bottom: 1px solid ${s.colorBorderSecondary};
  `,summaryItem:r`
    display: flex;
    align-items: baseline;
    gap: 6px;
  `,summaryValue:r`
    font-size: 20px;
    font-weight: 600;
    color: ${s.colorText};
  `,summaryLabel:r`
    font-size: 12px;
    color: ${s.colorTextSecondary};
  `,summaryDivider:r`
    width: 1px;
    height: 24px;
    background: ${s.colorBorderSecondary};
  `,chartArea:r`
    height: 140px;
    overflow-x: auto;
    overflow-y: hidden;
  `,emptyState:r`
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
  `,barsContainer:r`
    display: flex;
    align-items: flex-end;
    height: 100%;
    gap: 6px;
    padding: 0 4px;
  `,barWrapper:r`
    flex: 1 1 0;
    min-width: 60px;
    display: flex;
    flex-direction: column;
    align-items: center;
    height: 100%;
    cursor: pointer;

    &:hover .bar {
      opacity: 0.85;
      transform: scaleX(1.05);
    }
  `,barOuter:r`
    flex: 1;
    width: 100%;
    display: flex;
    align-items: flex-end;
    justify-content: center;
  `,bar:r`
    width: 100%;
    min-height: 0;
    border-radius: ${s.borderRadiusSM}px ${s.borderRadiusSM}px 0 0;
    display: flex;
    align-items: flex-start;
    justify-content: center;
    padding-top: 4px;
    transition: all 0.2s ease;
  `,barValue:r`
    font-size: 11px;
    font-weight: 600;
    color: white;
    text-shadow: 0 1px 2px rgba(0,0,0,0.3);
  `,barLabel:r`
    font-size: 10px;
    color: ${s.colorTextSecondary};
    text-align: center;
    margin-top: 6px;
    max-width: 100%;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
  `}}),j=e=>{let{data:r,loading:s,error:a,onBarClick:t}=e,{styles:o,theme:n}=v(),{t:i}=(0,f.useTranslation)();if(a)return(0,l.jsx)(y.A,{message:i("coreshop_messenger_error_loading",{defaultValue:"Error loading chart data"}),description:a,type:"error"});if(s)return(0,l.jsx)("div",{className:o.loadingContainer,children:(0,l.jsx)(b.A,{size:"large"})});let c=Math.max(...r.map(e=>e.count),1),d=r.reduce((e,r)=>e+r.count,0);return(0,l.jsxs)("div",{className:o.container,children:[(0,l.jsxs)("div",{className:o.summary,children:[(0,l.jsxs)("div",{className:o.summaryItem,children:[(0,l.jsx)("span",{className:o.summaryValue,children:d}),(0,l.jsx)("span",{className:o.summaryLabel,children:i("coreshop_messenger_total",{defaultValue:"Total"})})]}),(0,l.jsx)("div",{className:o.summaryDivider}),(0,l.jsxs)("div",{className:o.summaryItem,children:[(0,l.jsx)("span",{className:o.summaryValue,children:r.length}),(0,l.jsx)("span",{className:o.summaryLabel,children:i("coreshop_messenger_queues",{defaultValue:"Queues"})})]})]}),(0,l.jsx)("div",{className:o.chartArea,children:0===r.length?(0,l.jsx)("div",{className:o.emptyState,children:(0,l.jsx)(w,{type:"secondary",children:i("coreshop_messenger_no_data",{defaultValue:"No pending messages"})})}):(0,l.jsx)("div",{className:o.barsContainer,children:r.map((e,r)=>{let s,a=Math.max(e.count/c*100,15*(e.count>0)),i=(s=[n.colorPrimary,n.colorSuccess,n.colorWarning,n.colorInfo,"#722ed1","#13c2c2","#eb2f96","#fa8c16"])[r%s.length];return(0,l.jsx)(_.A,{title:`${e.receiver} (${e.count})`,placement:"top",children:(0,l.jsxs)("div",{className:o.barWrapper,onClick:()=>null==t?void 0:t(e.receiver),onKeyDown:r=>{("Enter"===r.key||" "===r.key)&&(null==t||t(e.receiver))},role:"button",tabIndex:0,children:[(0,l.jsx)("div",{className:o.barOuter,children:(0,l.jsx)("div",{className:o.bar,style:{height:`${a}%`,backgroundColor:i},children:e.count>0&&(0,l.jsx)("span",{className:o.barValue,children:e.count})})}),(0,l.jsx)("div",{className:o.barLabel,children:e.receiver})]})},e.receiver)})})})]})};var A=s(8421),S=s(8484),k=s(2912),C=s(9672),N=s(3014),V=s(3338),$=s(545),E=s(4971),M=s(6366),F=s(9504),T=s(2696);s(8268),s(9646),s(7602),s(1670),s(7350),s(3877),s(5579),s(6905),s(8385),s(3508);var I=s(2989);s(403),s(7857);let R=new class{async getMessageCount(){let e=await fetch(`${this.baseUrl}/messenger/count`);if(!e.ok)throw Error(`Failed to fetch message count: ${e.statusText}`);let r=await e.json();if(!r.success)throw Error("Failed to fetch message count");return r.data}async getFailureReceivers(){let e=await fetch(`${this.baseUrl}/messenger/list-failure-receivers`);if(!e.ok)throw Error(`Failed to fetch failure receivers: ${e.statusText}`);let r=await e.json();if(!r.success)throw Error("Failed to fetch failure receivers");return r.data}async getReceivers(){let e=await fetch(`${this.baseUrl}/messenger/list-receivers`);if(!e.ok)throw Error(`Failed to fetch receivers: ${e.statusText}`);let r=await e.json();if(!r.success)throw Error("Failed to fetch receivers");return r.data}async getFailedMessages(e){let r=await fetch(`${this.baseUrl}/messenger/list-failed/${encodeURIComponent(e)}`);if(!r.ok)throw Error(`Failed to fetch failed messages: ${r.statusText}`);let s=await r.json();if(!s.success)throw Error("Failed to fetch failed messages");return s.data}async getMessages(e){let r=await fetch(`${this.baseUrl}/messenger/list/${encodeURIComponent(e)}`);if(!r.ok)throw Error(`Failed to fetch messages: ${r.statusText}`);let s=await r.json();if(!s.success)throw Error("Failed to fetch messages");return s.data}async deleteFailedMessage(e,r){let s=await fetch(`${this.baseUrl}/messenger/delete/${encodeURIComponent(e)}`,{method:"DELETE",headers:{"Content-Type":"application/x-www-form-urlencoded"},body:new URLSearchParams({id:r})});if(!s.ok)throw Error(`Failed to delete message: ${s.statusText}`);let a=await s.json();if(!a.success)throw Error(a.message||"Failed to delete message")}async retryFailedMessage(e,r){let s=await fetch(`${this.baseUrl}/messenger/retry/${encodeURIComponent(e)}`,{method:"POST",headers:{"Content-Type":"application/x-www-form-urlencoded"},body:new URLSearchParams({id:r})});if(!s.ok)throw Error(`Failed to retry message: ${s.statusText}`);let a=await s.json();if(!a.success)throw Error(a.message||"Failed to retry message")}constructor(){this.baseUrl="/pimcore-studio/api/coreshop"}},z=new class{subscribe(e){return this.listeners.add(e),console.debug("MessengerEventEmitter: Subscribed, total listeners:",this.listeners.size),()=>{this.listeners.delete(e),console.debug("MessengerEventEmitter: Unsubscribed, total listeners:",this.listeners.size)}}emit(e){console.debug("MessengerEventEmitter: Emitting event to",this.listeners.size,"listeners",e),this.listeners.forEach(r=>{try{r(e)}catch(e){console.error("MessengerEventEmitter: Error in listener",e)}})}constructor(){this.listeners=new Set}};function B(e,r){let s=(0,o.useRef)(null),a=(0,o.useRef)(e);return a.current=e,(0,o.useCallback)(function(){for(var e=arguments.length,t=Array(e),l=0;l<e;l++)t[l]=arguments[l];s.current&&clearTimeout(s.current),s.current=setTimeout(()=>{a.current(...t)},r)},[r])}function L(){let[e,r]=(0,o.useState)([]),[s,a]=(0,o.useState)([]),[t,l]=(0,o.useState)(!0),[n,i]=(0,o.useState)(null),c=(0,o.useCallback)(async()=>{try{l(!0),i(null);let[e,s]=await Promise.all([R.getReceivers(),R.getFailureReceivers()]);r(e),a(s)}catch(e){i(e instanceof Error?e.message:"Failed to load receivers")}finally{l(!1)}},[]);return(0,o.useEffect)(()=>{c()},[c]),{receivers:e,failureReceivers:s,loading:t,error:n,reload:c}}let{Text:U,Paragraph:D}=x.A,P=(0,p.rU)(e=>{let{css:r,token:s}=e;return{container:r`
    height: 100%;
    display: flex;
    flex-direction: column;
  `,toolbar:r`
    display: flex;
    gap: 12px;
    align-items: center;
    margin-bottom: 16px;
    flex-shrink: 0;
  `,receiverSelect:r`
    width: 400px;
  `,errorAlert:r`
    margin-bottom: 16px;
    flex-shrink: 0;
  `,emptyState:r`
    flex: 1;
    display: flex;
    flex-direction: column;
    justify-content: center;
  `,table:r`
    flex: 1;

    .ant-table-thead > tr > th {
      background: ${s.colorBgLayout};
      font-weight: 600;
    }
  `,className:r`
    font-family: monospace;
    font-size: 12px;
    color: ${s.colorTextSecondary};
  `,dateCell:r`
    font-size: 12px;
    color: ${s.colorTextSecondary};
  `,modalContent:r`
    max-height: 500px;
    overflow: auto;
  `,modalMeta:r`
    margin-bottom: 12px;
  `,codeBlock:r`
    pre {
      background: ${s.colorBgLayout};
      padding: 12px;
      border-radius: ${s.borderRadius}px;
      font-size: 12px;
      white-space: pre-wrap;
      word-break: break-all;
      max-height: 400px;
      overflow: auto;
    }
  `,errorPre:r`
    white-space: pre-wrap;
    word-break: break-all;
    font-size: 12px;
    margin: 0;
  `}}),O=()=>{let{failureReceivers:e,loading:r}=L(),[s,a]=(0,o.useState)(null),[t,n]=(0,o.useState)(!1),[i,u]=(0,o.useState)(!1),[h,p]=(0,o.useState)(null),[x,b]=(0,o.useState)(new Set),{styles:w}=P(),v=(0,T.useMessage)(),{t:j}=(0,f.useTranslation)(),{messages:O,loading:K,error:W,reload:H,deleteMessage:Z,retryMessage:q}=function(e){let[r,s]=(0,o.useState)([]),[a,t]=(0,o.useState)(!1),[l,n]=(0,o.useState)(null),i=(0,o.useCallback)(async()=>{if(!e)return void s([]);try{t(!0),n(null);let r=await R.getFailedMessages(e);s(r)}catch(e){n(e instanceof Error?e.message:"Failed to load failed messages")}finally{t(!1)}},[e]),c=B(i,500),d=(0,o.useCallback)(async r=>{if(e)try{await R.deleteFailedMessage(e,r),await i()}catch(e){throw n(e instanceof Error?e.message:"Failed to delete message"),e}},[e,i]),u=(0,o.useCallback)(async r=>{if(e)try{await R.retryFailedMessage(e,r),await i()}catch(e){throw n(e instanceof Error?e.message:"Failed to retry message"),e}},[e,i]);return(0,o.useEffect)(()=>{i()},[i]),(0,o.useEffect)(()=>z.subscribe(r=>{e&&("message_failed"===r.type&&r.receiverName===e&&c(),("message_retried"===r.type||"message_rejected"===r.type)&&r.receiverName===e&&c())}),[e,c]),{messages:r,loading:a,error:l,reload:i,deleteMessage:d,retryMessage:u}}(s),Q=async e=>{let r=e.id;b(e=>new Set(e).add(`delete-${r}`));try{await Z(r),v.success(j("coreshop_messenger_delete_success",{defaultValue:"Message deleted successfully"}))}catch{v.error((0,I.ZF)(j("coreshop_messenger_delete_error",{defaultValue:"Failed to delete message"})))}finally{b(e=>{let s=new Set(e);return s.delete(`delete-${r}`),s})}},X=async e=>{let r=e.id;b(e=>new Set(e).add(`retry-${r}`));try{await q(r),v.success(j("coreshop_messenger_retry_success",{defaultValue:"Message retry initiated successfully"}))}catch{v.error((0,I.ZF)(j("coreshop_messenger_retry_error",{defaultValue:"Failed to retry message"})))}finally{b(e=>{let s=new Set(e);return s.delete(`retry-${r}`),s})}},G=e=>e.split("\\").pop()||e,J=[{title:"ID",dataIndex:"id",key:"id",width:80,render:e=>(0,l.jsx)(A.A,{children:e})},{title:j("coreshop_messenger_class",{defaultValue:"Class"}),dataIndex:"class",key:"class",ellipsis:!0,render:e=>(0,l.jsx)(_.A,{title:e,children:(0,l.jsx)("span",{className:w.className,children:G(e)})})},{title:j("coreshop_messenger_failed_at",{defaultValue:"Failed At"}),dataIndex:"failed_at",key:"failed_at",width:160,render:e=>{if(!e)return"-";let r=new Date(e);return(0,l.jsxs)("span",{className:w.dateCell,children:[r.toLocaleDateString()," ",r.toLocaleTimeString([],{hour:"2-digit",minute:"2-digit"})]})}},{title:j("coreshop_messenger_error",{defaultValue:"Error"}),dataIndex:"error",key:"error",ellipsis:!0,render:e=>(0,l.jsxs)(U,{type:"danger",ellipsis:{tooltip:e},children:[(0,l.jsx)(m.A,{style:{marginRight:4}}),e]})},{title:j("coreshop_messenger_actions",{defaultValue:"Actions"}),key:"actions",width:160,fixed:"right",render:(e,r)=>(0,l.jsxs)(c.A,{size:"small",children:[(0,l.jsx)(_.A,{title:j("coreshop_messenger_info",{defaultValue:"Details"}),children:(0,l.jsx)(d.Ay,{size:"small",icon:(0,l.jsx)($.A,{}),onClick:()=>{p(r),n(!0)}})}),(0,l.jsx)(_.A,{title:j("coreshop_messenger_show_error",{defaultValue:"Show error"}),children:(0,l.jsx)(d.Ay,{size:"small",danger:!0,icon:(0,l.jsx)(E.A,{}),onClick:()=>{p(r),u(!0)}})}),(0,l.jsx)(S.A,{title:j("coreshop_messenger_delete_failed_message",{defaultValue:"Delete failed Message"}),description:j("coreshop_messenger_delete_confirm",{defaultValue:"Are you sure you want to delete this message?"}),onConfirm:()=>Q(r),okText:j("coreshop_messenger_delete",{defaultValue:"Delete"}),cancelText:j("coreshop_messenger_cancel",{defaultValue:"Cancel"}),children:(0,l.jsx)(_.A,{title:j("coreshop_messenger_delete",{defaultValue:"Delete"}),children:(0,l.jsx)(d.Ay,{size:"small",danger:!0,icon:(0,l.jsx)(M.A,{}),loading:x.has(`delete-${r.id}`)})})}),(0,l.jsx)(S.A,{title:j("coreshop_messenger_retry_failed_message",{defaultValue:"Retry failed Message"}),description:j("coreshop_messenger_retry_confirm",{defaultValue:"Are you sure you want to retry this message?"}),onConfirm:()=>X(r),okText:j("coreshop_messenger_retry",{defaultValue:"Retry"}),cancelText:j("coreshop_messenger_cancel",{defaultValue:"Cancel"}),children:(0,l.jsx)(_.A,{title:j("coreshop_messenger_retry",{defaultValue:"Retry"}),children:(0,l.jsx)(d.Ay,{size:"small",type:"primary",icon:(0,l.jsx)(F.A,{}),loading:x.has(`retry-${r.id}`)})})})]})}];return(0,l.jsxs)("div",{className:w.container,children:[(0,l.jsxs)("div",{className:w.toolbar,children:[(0,l.jsx)(k.A,{className:w.receiverSelect,placeholder:j("coreshop_messenger_failure_receivers",{defaultValue:"Select failure receiver"}),value:s,onChange:e=>{a(e)},loading:r,allowClear:!0,showSearch:!0,filterOption:(e,r)=>{var s;return null==r||null==(s=r.children)?void 0:s.toLowerCase().includes(e.toLowerCase())},children:e.map(e=>(0,l.jsx)(k.A.Option,{value:e.receiver,children:e.receiver},e.receiver))}),(0,l.jsx)(d.Ay,{icon:(0,l.jsx)(g.A,{}),onClick:H,disabled:!s,children:j("coreshop_messenger_reload",{defaultValue:"Reload"})})]}),W&&(0,l.jsx)(y.A,{message:j("coreshop_messenger_error_loading",{defaultValue:"Error loading messages"}),description:W,type:"error",className:w.errorAlert,closable:!0}),s?(0,l.jsx)(N.A,{columns:J,dataSource:O,rowKey:"id",loading:K,scroll:{y:"calc(100vh - 520px)"},pagination:!1,size:"small",className:w.table}):(0,l.jsx)(C.A,{className:w.emptyState,description:j("coreshop_messenger_select_receiver",{defaultValue:"Please select a receiver to view failed messages"})}),(0,l.jsx)(V.A,{title:j("coreshop_messenger_message_info",{defaultValue:"Message Information"}),open:t,onCancel:()=>n(!1),footer:[(0,l.jsx)(d.Ay,{onClick:()=>n(!1),children:j("coreshop_messenger_close",{defaultValue:"Close"})},"close")],width:700,children:h&&(0,l.jsxs)("div",{className:w.modalContent,children:[(0,l.jsxs)("div",{className:w.modalMeta,children:[(0,l.jsxs)(A.A,{children:["ID: ",h.id]}),(0,l.jsx)(A.A,{color:"blue",children:G(h.class)})]}),(0,l.jsx)(D,{className:w.codeBlock,children:(0,l.jsx)("pre",{children:h.serialized||j("coreshop_messenger_no_data",{defaultValue:"No data available"})})})]})}),(0,l.jsx)(V.A,{title:j("coreshop_messenger_error_details",{defaultValue:"Error Details"}),open:i,onCancel:()=>u(!1),footer:[(0,l.jsx)(d.Ay,{onClick:()=>u(!1),children:j("coreshop_messenger_close",{defaultValue:"Close"})},"close")],width:700,children:h&&(0,l.jsx)("div",{className:w.modalContent,children:(0,l.jsx)(y.A,{message:j("coreshop_messenger_error",{defaultValue:"Error"}),description:(0,l.jsx)("pre",{className:w.errorPre,children:h.error||j("coreshop_messenger_no_error",{defaultValue:"No error information available"})}),type:"error",showIcon:!0})})})]})},{Paragraph:K}=x.A,W=(0,p.rU)(e=>{let{css:r,token:s}=e;return{container:r`
    height: 100%;
    display: flex;
    flex-direction: column;
  `,toolbar:r`
    display: flex;
    gap: 12px;
    align-items: center;
    margin-bottom: 16px;
    flex-shrink: 0;
  `,receiverSelect:r`
    width: 400px;
  `,errorAlert:r`
    margin-bottom: 16px;
    flex-shrink: 0;
  `,emptyState:r`
    flex: 1;
    display: flex;
    flex-direction: column;
    justify-content: center;
  `,table:r`
    flex: 1;

    .ant-table-thead > tr > th {
      background: ${s.colorBgLayout};
      font-weight: 600;
    }
  `,className:r`
    font-family: monospace;
    font-size: 12px;
    color: ${s.colorTextSecondary};
  `,modalContent:r`
    max-height: 500px;
    overflow: auto;
  `,modalMeta:r`
    margin-bottom: 12px;
  `,codeBlock:r`
    pre {
      background: ${s.colorBgLayout};
      padding: 12px;
      border-radius: ${s.borderRadius}px;
      font-size: 12px;
      white-space: pre-wrap;
      word-break: break-all;
      max-height: 400px;
      overflow: auto;
    }
  `}}),H=e=>{let{selectedReceiver:r}=e,{receivers:s,loading:a}=L(),[t,n]=(0,o.useState)(r??null),[i,u]=(0,o.useState)(!1),[h,m]=(0,o.useState)(null),{styles:p}=W(),{t:x}=(0,f.useTranslation)(),{messages:b,loading:w,error:v,reload:j}=function(e){let[r,s]=(0,o.useState)([]),[a,t]=(0,o.useState)(!1),[l,n]=(0,o.useState)(null),i=(0,o.useCallback)(async()=>{if(!e)return void s([]);try{t(!0),n(null);let r=await R.getMessages(e);s(r)}catch(e){n(e instanceof Error?e.message:"Failed to load messages")}finally{t(!1)}},[e]),c=B(i,500);return(0,o.useEffect)(()=>{i()},[i]),(0,o.useEffect)(()=>z.subscribe(r=>{e&&"message_handled"===r.type&&r.receiverName===e&&c()}),[e,c]),{messages:r,loading:a,error:l,reload:i}}(t);(0,o.useEffect)(()=>{null!=r&&n(r)},[r]);let S=e=>e.split("\\").pop()||e,E=[{title:"ID",dataIndex:"id",key:"id",width:100,render:e=>(0,l.jsx)(A.A,{children:e})},{title:x("coreshop_messenger_class",{defaultValue:"Class"}),dataIndex:"class",key:"class",ellipsis:!0,render:e=>(0,l.jsx)(_.A,{title:e,children:(0,l.jsx)("span",{className:p.className,children:S(e)})})},{title:x("coreshop_messenger_actions",{defaultValue:"Actions"}),key:"actions",width:80,fixed:"right",render:(e,r)=>(0,l.jsx)(c.A,{size:"small",children:(0,l.jsx)(_.A,{title:x("coreshop_messenger_info",{defaultValue:"Details"}),children:(0,l.jsx)(d.Ay,{size:"small",icon:(0,l.jsx)($.A,{}),onClick:()=>{m(r),u(!0)}})})})}];return(0,l.jsxs)("div",{className:p.container,children:[(0,l.jsxs)("div",{className:p.toolbar,children:[(0,l.jsx)(k.A,{className:p.receiverSelect,placeholder:x("coreshop_messenger_receivers",{defaultValue:"Select receiver"}),value:t,onChange:e=>{n(e)},loading:a,allowClear:!0,showSearch:!0,filterOption:(e,r)=>{var s;return null==r||null==(s=r.children)?void 0:s.toLowerCase().includes(e.toLowerCase())},children:s.map(e=>(0,l.jsx)(k.A.Option,{value:e.receiver,children:e.receiver},e.receiver))}),(0,l.jsx)(d.Ay,{icon:(0,l.jsx)(g.A,{}),onClick:j,disabled:!t,children:x("coreshop_messenger_reload",{defaultValue:"Reload"})})]}),v&&(0,l.jsx)(y.A,{message:x("coreshop_messenger_error_loading",{defaultValue:"Error loading messages"}),description:v,type:"error",className:p.errorAlert,closable:!0}),t?(0,l.jsx)(N.A,{columns:E,dataSource:b,rowKey:"id",loading:w,scroll:{y:"calc(100vh - 520px)"},pagination:!1,size:"small",className:p.table}):(0,l.jsx)(C.A,{className:p.emptyState,description:x("coreshop_messenger_select_receiver_pending",{defaultValue:"Please select a receiver to view pending messages"})}),(0,l.jsx)(V.A,{title:x("coreshop_messenger_message_info",{defaultValue:"Message Information"}),open:i,onCancel:()=>u(!1),footer:[(0,l.jsx)(d.Ay,{onClick:()=>u(!1),children:x("coreshop_messenger_close",{defaultValue:"Close"})},"close")],width:700,children:h&&(0,l.jsxs)("div",{className:p.modalContent,children:[(0,l.jsxs)("div",{className:p.modalMeta,children:[(0,l.jsxs)(A.A,{children:["ID: ",h.id]}),(0,l.jsx)(A.A,{color:"blue",children:S(h.class)})]}),(0,l.jsx)(K,{className:p.codeBlock,children:(0,l.jsx)("pre",{children:h.serialized||x("coreshop_messenger_no_data",{defaultValue:"No data available"})})})]})})]})},Z=()=>{let{data:e,loading:r,error:s,reload:a}=function(){let[e,r]=(0,o.useState)([]),[s,a]=(0,o.useState)(!0),[t,l]=(0,o.useState)(null),n=(0,o.useCallback)(async()=>{try{a(!0),l(null);let e=await R.getMessageCount();r(e)}catch(e){l(e instanceof Error?e.message:"Failed to load chart data")}finally{a(!1)}},[]),i=B(n,500);return(0,o.useEffect)(()=>{n()},[n]),(0,o.useEffect)(()=>z.subscribe(e=>{console.debug("useMessengerChart: Received Mercure event, triggering reload"),i()}),[i]),{data:e,loading:s,error:t,reload:n}}(),{styles:t}=q(),{t:n}=(0,f.useTranslation)(),[i,p]=o.useState("pending"),[x,y]=o.useState(null),b=[{key:"pending",label:(0,l.jsxs)(c.A,{size:4,children:[(0,l.jsx)(h.A,{}),n("coreshop_messenger_pending_messages",{defaultValue:"Pending Messages"})]}),children:(0,l.jsx)("div",{className:t.tabContent,children:(0,l.jsx)(H,{selectedReceiver:x})})},{key:"failed",label:(0,l.jsxs)(c.A,{size:4,children:[(0,l.jsx)(m.A,{}),n("coreshop_messenger_failed_messages",{defaultValue:"Failed Messages"})]}),children:(0,l.jsx)("div",{className:t.tabContent,children:(0,l.jsx)(O,{})})}];return(0,l.jsxs)("div",{className:t.container,children:[(0,l.jsxs)("div",{className:t.header,children:[(0,l.jsx)("div",{className:t.title,children:n("coreshop_messenger",{defaultValue:"Messenger"})}),(0,l.jsx)(d.Ay,{type:"primary",icon:(0,l.jsx)(g.A,{}),onClick:()=>{a()},children:n("coreshop_messenger_reload_all",{defaultValue:"Reload"})})]}),(0,l.jsx)("div",{className:t.chartSection,children:(0,l.jsx)(j,{data:e,loading:r,error:s,onBarClick:e=>{y(e),p("pending")}})}),(0,l.jsx)("div",{className:t.tabsSection,children:(0,l.jsx)(u.A,{activeKey:i,onChange:p,type:"card",items:b,className:t.tabs})})]})},q=(0,p.rU)(e=>{let{css:r,token:s}=e;return{container:r`
    height: 100%;
    display: flex;
    flex-direction: column;
    padding: 16px;
    overflow: hidden;
    background: ${s.colorBgLayout};
  `,header:r`
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 16px;
    flex-shrink: 0;
  `,title:r`
    font-size: 18px;
    font-weight: 600;
    color: ${s.colorText};
  `,chartSection:r`
    flex-shrink: 0;
  `,tabsSection:r`
    flex: 1;
    min-height: 0;
    overflow: hidden;
    display: flex;
    flex-direction: column;
  `,tabs:r`
    height: 100%;
    display: flex;
    flex-direction: column;

    .ant-tabs-nav {
      margin-bottom: 0;
      flex-shrink: 0;

      .ant-tabs-tab {
        padding: 8px 16px;

        &.ant-tabs-tab-active {
          background: ${s.colorBgContainer};
        }
      }
    }

    .ant-tabs-content-holder {
      flex: 1;
      overflow: hidden;
      background: ${s.colorBgContainer};
      border: 1px solid ${s.colorBorderSecondary};
      border-top: none;
      border-radius: 0 0 ${s.borderRadius}px ${s.borderRadius}px;
    }

    .ant-tabs-content {
      height: 100%;
    }

    .ant-tabs-tabpane {
      height: 100%;
      overflow: hidden;
    }
  `,tabContent:r`
    height: 100%;
    overflow: hidden;
    padding: 16px;
  `}}),Q={onInit(){a.container.get(t.serviceIds.widgetManager).registerWidget({name:"coreshop-messenger-widget",component:Z})}};class X{getId(){return this.handlerId}shouldHandle(e){return"update"===e.type&&!!e.payload&&"coreshop.messenger.update"===e.payload.eventType}async handleMessage(e){if(!e.payload)return;let r=e.payload;z.emit(r)}onRegister(){}onUnregister(){}constructor(){this.handlerId="coreshop-messenger-handler"}}let G={name:"coreshop-messenger",onInit(){try{let e=a.container.get(t.serviceIds.globalMessageBus),r=new X;e.registerHandler(r)}catch(e){console.warn("CoreShop MessengerBundle: Failed to register Mercure message handler",e)}},onStartup(e){let{moduleSystem:r}=e;r.registerModule(i),r.registerModule(Q)}}},2989:function(e,r,s){s.d(r,{ZF:()=>l});var a=s(7185),t=s.n(a);s(2703);let l=e=>"string"==typeof e&&e.includes("\n")?t().createElement("div",null,...e.split("\n").map((e,r)=>t().createElement("div",{key:r},e))):e}}]);