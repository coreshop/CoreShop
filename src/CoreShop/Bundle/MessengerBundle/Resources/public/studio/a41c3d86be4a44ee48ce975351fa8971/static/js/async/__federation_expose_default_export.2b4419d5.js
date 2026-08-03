/*! For license information please see __federation_expose_default_export.2b4419d5.js.LICENSE.txt */
"use strict";(self["chunk_coreshopmessenger "]=self["chunk_coreshopmessenger "]||[]).push([["525"],{3676:function(e,r,s){s.r(r),s.d(r,{default:()=>F});var t=s(2977),a=s(4781),l=s(2855),o=s(5168);let n=e=>(0,l.jsxs)("svg",{xmlns:"http://www.w3.org/2000/svg",viewBox:"0 0 48 48",width:"1em",height:"1em",...e,children:[(0,l.jsx)("path",{fill:"#2196F3",d:"M40 10H8a4 4 0 0 0-4 4v20a4 4 0 0 0 4 4h32a4 4 0 0 0 4-4V14a4 4 0 0 0-4-4"}),(0,l.jsx)("path",{fill:"#0D47A1",d:"M44 14.025a4 4 0 0 0-.24-1.32L24 27.025 4.241 12.705A4 4 0 0 0 4 14.025V15l20 14.495L44 15z"})]}),i={onInit(){let e=t.container.get(a.serviceIds.iconLibrary);e.register({name:"coreshop_messenger",component:n}),e.register({name:"coreshop_nav_icon_messenger",component:n})}};var c=s(8701),d=s(3033),u=s(6345),h=s(5210);let{Text:m}=c.Typography,g=(0,u.createStyles)(e=>{let{css:r,token:s}=e;return{container:r`
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
  `}}),p=e=>{let{data:r,loading:s,error:t,onBarClick:a}=e,{styles:o,theme:n}=g(),{t:i}=(0,h.useTranslation)();if(t)return(0,l.jsx)(c.Alert,{message:i("coreshop_messenger_error_loading",{defaultValue:"Error loading chart data"}),description:t,type:"error"});if(s)return(0,l.jsx)("div",{className:o.loadingContainer,children:(0,l.jsx)(c.Spin,{size:"large"})});let d=Math.max(...r.map(e=>e.count),1),u=r.reduce((e,r)=>e+r.count,0);return(0,l.jsxs)("div",{className:o.container,children:[(0,l.jsxs)("div",{className:o.summary,children:[(0,l.jsxs)("div",{className:o.summaryItem,children:[(0,l.jsx)("span",{className:o.summaryValue,children:u}),(0,l.jsx)("span",{className:o.summaryLabel,children:i("coreshop_messenger_total",{defaultValue:"Total"})})]}),(0,l.jsx)("div",{className:o.summaryDivider}),(0,l.jsxs)("div",{className:o.summaryItem,children:[(0,l.jsx)("span",{className:o.summaryValue,children:r.length}),(0,l.jsx)("span",{className:o.summaryLabel,children:i("coreshop_messenger_queues",{defaultValue:"Queues"})})]})]}),(0,l.jsx)("div",{className:o.chartArea,children:0===r.length?(0,l.jsx)("div",{className:o.emptyState,children:(0,l.jsx)(m,{type:"secondary",children:i("coreshop_messenger_no_data",{defaultValue:"No pending messages"})})}):(0,l.jsx)("div",{className:o.barsContainer,children:r.map((e,r)=>{let s,t=Math.max(e.count/d*100,15*(e.count>0)),i=(s=[n.colorPrimary,n.colorSuccess,n.colorWarning,n.colorInfo,"#722ed1","#13c2c2","#eb2f96","#fa8c16"])[r%s.length];return(0,l.jsx)(c.Tooltip,{title:`${e.receiver} (${e.count})`,placement:"top",children:(0,l.jsxs)("div",{className:o.barWrapper,onClick:()=>null==a?void 0:a(e.receiver),onKeyDown:r=>{("Enter"===r.key||" "===r.key)&&(null==a||a(e.receiver))},role:"button",tabIndex:0,children:[(0,l.jsx)("div",{className:o.barOuter,children:(0,l.jsx)("div",{className:o.bar,style:{height:`${t}%`,backgroundColor:i},children:e.count>0&&(0,l.jsx)("span",{className:o.barValue,children:e.count})})}),(0,l.jsx)("div",{className:o.barLabel,children:e.receiver})]})},e.receiver)})})})]})};var f=s(2696);s(8268),s(9646),s(7602),s(1670),s(7350),s(3877),s(5579),s(6905),s(8385),s(3508);var x=s(2989);s(403),s(238);let y=new class{async getMessageCount(){let e=await fetch(`${this.baseUrl}/messenger/count`);if(!e.ok)throw Error(`Failed to fetch message count: ${e.statusText}`);let r=await e.json();if(!r.success)throw Error("Failed to fetch message count");return r.data}async getFailureReceivers(){let e=await fetch(`${this.baseUrl}/messenger/list-failure-receivers`);if(!e.ok)throw Error(`Failed to fetch failure receivers: ${e.statusText}`);let r=await e.json();if(!r.success)throw Error("Failed to fetch failure receivers");return r.data}async getReceivers(){let e=await fetch(`${this.baseUrl}/messenger/list-receivers`);if(!e.ok)throw Error(`Failed to fetch receivers: ${e.statusText}`);let r=await e.json();if(!r.success)throw Error("Failed to fetch receivers");return r.data}async getFailedMessages(e){let r=await fetch(`${this.baseUrl}/messenger/list-failed/${encodeURIComponent(e)}`);if(!r.ok)throw Error(`Failed to fetch failed messages: ${r.statusText}`);let s=await r.json();if(!s.success)throw Error("Failed to fetch failed messages");return s.data}async getMessages(e){let r=await fetch(`${this.baseUrl}/messenger/list/${encodeURIComponent(e)}`);if(!r.ok)throw Error(`Failed to fetch messages: ${r.statusText}`);let s=await r.json();if(!s.success)throw Error("Failed to fetch messages");return s.data}async deleteFailedMessage(e,r){let s=await fetch(`${this.baseUrl}/messenger/delete/${encodeURIComponent(e)}`,{method:"DELETE",headers:{"Content-Type":"application/x-www-form-urlencoded"},body:new URLSearchParams({id:r})});if(!s.ok)throw Error(`Failed to delete message: ${s.statusText}`);let t=await s.json();if(!t.success)throw Error(t.message||"Failed to delete message")}async retryFailedMessage(e,r){let s=await fetch(`${this.baseUrl}/messenger/retry/${encodeURIComponent(e)}`,{method:"POST",headers:{"Content-Type":"application/x-www-form-urlencoded"},body:new URLSearchParams({id:r})});if(!s.ok)throw Error(`Failed to retry message: ${s.statusText}`);let t=await s.json();if(!t.success)throw Error(t.message||"Failed to retry message")}constructor(){this.baseUrl="/pimcore-studio/api/coreshop"}},b=new class{subscribe(e){return this.listeners.add(e),console.debug("MessengerEventEmitter: Subscribed, total listeners:",this.listeners.size),()=>{this.listeners.delete(e),console.debug("MessengerEventEmitter: Unsubscribed, total listeners:",this.listeners.size)}}emit(e){console.debug("MessengerEventEmitter: Emitting event to",this.listeners.size,"listeners",e),this.listeners.forEach(r=>{try{r(e)}catch(e){console.error("MessengerEventEmitter: Error in listener",e)}})}constructor(){this.listeners=new Set}};function _(e,r){let s=(0,o.useRef)(null),t=(0,o.useRef)(e);return t.current=e,(0,o.useCallback)(function(){for(var e=arguments.length,a=Array(e),l=0;l<e;l++)a[l]=arguments[l];s.current&&clearTimeout(s.current),s.current=setTimeout(()=>{t.current(...a)},r)},[r])}function w(){let[e,r]=(0,o.useState)([]),[s,t]=(0,o.useState)([]),[a,l]=(0,o.useState)(!0),[n,i]=(0,o.useState)(null),c=(0,o.useCallback)(async()=>{try{l(!0),i(null);let[e,s]=await Promise.all([y.getReceivers(),y.getFailureReceivers()]);r(e),t(s)}catch(e){i(e instanceof Error?e.message:"Failed to load receivers")}finally{l(!1)}},[]);return(0,o.useEffect)(()=>{c()},[c]),{receivers:e,failureReceivers:s,loading:a,error:n,reload:c}}let{Text:v,Paragraph:j}=c.Typography,S=(0,u.createStyles)(e=>{let{css:r,token:s}=e;return{container:r`
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
  `}}),C=()=>{let{failureReceivers:e,loading:r}=w(),[s,t]=(0,o.useState)(null),[a,n]=(0,o.useState)(!1),[i,u]=(0,o.useState)(!1),[m,g]=(0,o.useState)(null),[p,C]=(0,o.useState)(new Set),{styles:k}=S(),N=(0,f.useMessage)(),{t:V}=(0,h.useTranslation)(),{messages:$,loading:E,error:T,reload:M,deleteMessage:F,retryMessage:R}=function(e){let[r,s]=(0,o.useState)([]),[t,a]=(0,o.useState)(!1),[l,n]=(0,o.useState)(null),i=(0,o.useCallback)(async()=>{if(!e)return void s([]);try{a(!0),n(null);let r=await y.getFailedMessages(e);s(r)}catch(e){n(e instanceof Error?e.message:"Failed to load failed messages")}finally{a(!1)}},[e]),c=_(i,500),d=(0,o.useCallback)(async r=>{if(e)try{await y.deleteFailedMessage(e,r),await i()}catch(e){throw n(e instanceof Error?e.message:"Failed to delete message"),e}},[e,i]),u=(0,o.useCallback)(async r=>{if(e)try{await y.retryFailedMessage(e,r),await i()}catch(e){throw n(e instanceof Error?e.message:"Failed to retry message"),e}},[e,i]);return(0,o.useEffect)(()=>{i()},[i]),(0,o.useEffect)(()=>b.subscribe(r=>{e&&("message_failed"===r.type&&r.receiverName===e&&c(),("message_retried"===r.type||"message_rejected"===r.type)&&r.receiverName===e&&c())}),[e,c]),{messages:r,loading:t,error:l,reload:i,deleteMessage:d,retryMessage:u}}(s),B=async e=>{let r=e.id;C(e=>new Set(e).add(`delete-${r}`));try{await F(r),N.success(V("coreshop_messenger_delete_success",{defaultValue:"Message deleted successfully"}))}catch{N.error((0,x.ZF)(V("coreshop_messenger_delete_error",{defaultValue:"Failed to delete message"})))}finally{C(e=>{let s=new Set(e);return s.delete(`delete-${r}`),s})}},I=async e=>{let r=e.id;C(e=>new Set(e).add(`retry-${r}`));try{await R(r),N.success(V("coreshop_messenger_retry_success",{defaultValue:"Message retry initiated successfully"}))}catch{N.error((0,x.ZF)(V("coreshop_messenger_retry_error",{defaultValue:"Failed to retry message"})))}finally{C(e=>{let s=new Set(e);return s.delete(`retry-${r}`),s})}},z=e=>e.split("\\").pop()||e,L=[{title:"ID",dataIndex:"id",key:"id",width:80,render:e=>(0,l.jsx)(c.Tag,{children:e})},{title:V("coreshop_messenger_class",{defaultValue:"Class"}),dataIndex:"class",key:"class",ellipsis:!0,render:e=>(0,l.jsx)(c.Tooltip,{title:e,children:(0,l.jsx)("span",{className:k.className,children:z(e)})})},{title:V("coreshop_messenger_failed_at",{defaultValue:"Failed At"}),dataIndex:"failed_at",key:"failed_at",width:160,render:e=>{if(!e)return"-";let r=new Date(e);return(0,l.jsxs)("span",{className:k.dateCell,children:[r.toLocaleDateString()," ",r.toLocaleTimeString([],{hour:"2-digit",minute:"2-digit"})]})}},{title:V("coreshop_messenger_error",{defaultValue:"Error"}),dataIndex:"error",key:"error",ellipsis:!0,render:e=>(0,l.jsxs)(v,{type:"danger",ellipsis:{tooltip:e},children:[(0,l.jsx)(d.WarningOutlined,{style:{marginRight:4}}),e]})},{title:V("coreshop_messenger_actions",{defaultValue:"Actions"}),key:"actions",width:160,fixed:"right",render:(e,r)=>(0,l.jsxs)(c.Space,{size:"small",children:[(0,l.jsx)(c.Tooltip,{title:V("coreshop_messenger_info",{defaultValue:"Details"}),children:(0,l.jsx)(c.Button,{size:"small",icon:(0,l.jsx)(d.InfoCircleOutlined,{}),onClick:()=>{g(r),n(!0)}})}),(0,l.jsx)(c.Tooltip,{title:V("coreshop_messenger_show_error",{defaultValue:"Show error"}),children:(0,l.jsx)(c.Button,{size:"small",danger:!0,icon:(0,l.jsx)(d.ExclamationCircleOutlined,{}),onClick:()=>{g(r),u(!0)}})}),(0,l.jsx)(c.Popconfirm,{title:V("coreshop_messenger_delete_failed_message",{defaultValue:"Delete failed Message"}),description:V("coreshop_messenger_delete_confirm",{defaultValue:"Are you sure you want to delete this message?"}),onConfirm:()=>B(r),okText:V("coreshop_messenger_delete",{defaultValue:"Delete"}),cancelText:V("coreshop_messenger_cancel",{defaultValue:"Cancel"}),children:(0,l.jsx)(c.Tooltip,{title:V("coreshop_messenger_delete",{defaultValue:"Delete"}),children:(0,l.jsx)(c.Button,{size:"small",danger:!0,icon:(0,l.jsx)(d.DeleteOutlined,{}),loading:p.has(`delete-${r.id}`)})})}),(0,l.jsx)(c.Popconfirm,{title:V("coreshop_messenger_retry_failed_message",{defaultValue:"Retry failed Message"}),description:V("coreshop_messenger_retry_confirm",{defaultValue:"Are you sure you want to retry this message?"}),onConfirm:()=>I(r),okText:V("coreshop_messenger_retry",{defaultValue:"Retry"}),cancelText:V("coreshop_messenger_cancel",{defaultValue:"Cancel"}),children:(0,l.jsx)(c.Tooltip,{title:V("coreshop_messenger_retry",{defaultValue:"Retry"}),children:(0,l.jsx)(c.Button,{size:"small",type:"primary",icon:(0,l.jsx)(d.RedoOutlined,{}),loading:p.has(`retry-${r.id}`)})})})]})}];return(0,l.jsxs)("div",{className:k.container,children:[(0,l.jsxs)("div",{className:k.toolbar,children:[(0,l.jsx)(c.Select,{className:k.receiverSelect,placeholder:V("coreshop_messenger_failure_receivers",{defaultValue:"Select failure receiver"}),value:s,onChange:e=>{t(e)},loading:r,allowClear:!0,showSearch:!0,filterOption:(e,r)=>{var s;return null==r||null==(s=r.children)?void 0:s.toLowerCase().includes(e.toLowerCase())},children:e.map(e=>(0,l.jsx)(c.Select.Option,{value:e.receiver,children:e.receiver},e.receiver))}),(0,l.jsx)(c.Button,{icon:(0,l.jsx)(d.ReloadOutlined,{}),onClick:M,disabled:!s,children:V("coreshop_messenger_reload",{defaultValue:"Reload"})})]}),T&&(0,l.jsx)(c.Alert,{message:V("coreshop_messenger_error_loading",{defaultValue:"Error loading messages"}),description:T,type:"error",className:k.errorAlert,closable:!0}),s?(0,l.jsx)(c.Table,{columns:L,dataSource:$,rowKey:"id",loading:E,scroll:{y:"calc(100vh - 520px)"},pagination:!1,size:"small",className:k.table}):(0,l.jsx)(c.Empty,{className:k.emptyState,description:V("coreshop_messenger_select_receiver",{defaultValue:"Please select a receiver to view failed messages"})}),(0,l.jsx)(c.Modal,{title:V("coreshop_messenger_message_info",{defaultValue:"Message Information"}),open:a,onCancel:()=>n(!1),footer:[(0,l.jsx)(c.Button,{onClick:()=>n(!1),children:V("coreshop_messenger_close",{defaultValue:"Close"})},"close")],width:700,children:m&&(0,l.jsxs)("div",{className:k.modalContent,children:[(0,l.jsxs)("div",{className:k.modalMeta,children:[(0,l.jsxs)(c.Tag,{children:["ID: ",m.id]}),(0,l.jsx)(c.Tag,{color:"blue",children:z(m.class)})]}),(0,l.jsx)(j,{className:k.codeBlock,children:(0,l.jsx)("pre",{children:m.serialized||V("coreshop_messenger_no_data",{defaultValue:"No data available"})})})]})}),(0,l.jsx)(c.Modal,{title:V("coreshop_messenger_error_details",{defaultValue:"Error Details"}),open:i,onCancel:()=>u(!1),footer:[(0,l.jsx)(c.Button,{onClick:()=>u(!1),children:V("coreshop_messenger_close",{defaultValue:"Close"})},"close")],width:700,children:m&&(0,l.jsx)("div",{className:k.modalContent,children:(0,l.jsx)(c.Alert,{message:V("coreshop_messenger_error",{defaultValue:"Error"}),description:(0,l.jsx)("pre",{className:k.errorPre,children:m.error||V("coreshop_messenger_no_error",{defaultValue:"No error information available"})}),type:"error",showIcon:!0})})})]})},{Paragraph:k}=c.Typography,N=(0,u.createStyles)(e=>{let{css:r,token:s}=e;return{container:r`
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
  `}}),V=e=>{let{selectedReceiver:r}=e,{receivers:s,loading:t}=w(),[a,n]=(0,o.useState)(r??null),[i,u]=(0,o.useState)(!1),[m,g]=(0,o.useState)(null),{styles:p}=N(),{t:f}=(0,h.useTranslation)(),{messages:x,loading:v,error:j,reload:S}=function(e){let[r,s]=(0,o.useState)([]),[t,a]=(0,o.useState)(!1),[l,n]=(0,o.useState)(null),i=(0,o.useCallback)(async()=>{if(!e)return void s([]);try{a(!0),n(null);let r=await y.getMessages(e);s(r)}catch(e){n(e instanceof Error?e.message:"Failed to load messages")}finally{a(!1)}},[e]),c=_(i,500);return(0,o.useEffect)(()=>{i()},[i]),(0,o.useEffect)(()=>b.subscribe(r=>{e&&"message_handled"===r.type&&r.receiverName===e&&c()}),[e,c]),{messages:r,loading:t,error:l,reload:i}}(a);(0,o.useEffect)(()=>{null!=r&&n(r)},[r]);let C=e=>e.split("\\").pop()||e,V=[{title:"ID",dataIndex:"id",key:"id",width:100,render:e=>(0,l.jsx)(c.Tag,{children:e})},{title:f("coreshop_messenger_class",{defaultValue:"Class"}),dataIndex:"class",key:"class",ellipsis:!0,render:e=>(0,l.jsx)(c.Tooltip,{title:e,children:(0,l.jsx)("span",{className:p.className,children:C(e)})})},{title:f("coreshop_messenger_actions",{defaultValue:"Actions"}),key:"actions",width:80,fixed:"right",render:(e,r)=>(0,l.jsx)(c.Space,{size:"small",children:(0,l.jsx)(c.Tooltip,{title:f("coreshop_messenger_info",{defaultValue:"Details"}),children:(0,l.jsx)(c.Button,{size:"small",icon:(0,l.jsx)(d.InfoCircleOutlined,{}),onClick:()=>{g(r),u(!0)}})})})}];return(0,l.jsxs)("div",{className:p.container,children:[(0,l.jsxs)("div",{className:p.toolbar,children:[(0,l.jsx)(c.Select,{className:p.receiverSelect,placeholder:f("coreshop_messenger_receivers",{defaultValue:"Select receiver"}),value:a,onChange:e=>{n(e)},loading:t,allowClear:!0,showSearch:!0,filterOption:(e,r)=>{var s;return null==r||null==(s=r.children)?void 0:s.toLowerCase().includes(e.toLowerCase())},children:s.map(e=>(0,l.jsx)(c.Select.Option,{value:e.receiver,children:e.receiver},e.receiver))}),(0,l.jsx)(c.Button,{icon:(0,l.jsx)(d.ReloadOutlined,{}),onClick:S,disabled:!a,children:f("coreshop_messenger_reload",{defaultValue:"Reload"})})]}),j&&(0,l.jsx)(c.Alert,{message:f("coreshop_messenger_error_loading",{defaultValue:"Error loading messages"}),description:j,type:"error",className:p.errorAlert,closable:!0}),a?(0,l.jsx)(c.Table,{columns:V,dataSource:x,rowKey:"id",loading:v,scroll:{y:"calc(100vh - 520px)"},pagination:!1,size:"small",className:p.table}):(0,l.jsx)(c.Empty,{className:p.emptyState,description:f("coreshop_messenger_select_receiver_pending",{defaultValue:"Please select a receiver to view pending messages"})}),(0,l.jsx)(c.Modal,{title:f("coreshop_messenger_message_info",{defaultValue:"Message Information"}),open:i,onCancel:()=>u(!1),footer:[(0,l.jsx)(c.Button,{onClick:()=>u(!1),children:f("coreshop_messenger_close",{defaultValue:"Close"})},"close")],width:700,children:m&&(0,l.jsxs)("div",{className:p.modalContent,children:[(0,l.jsxs)("div",{className:p.modalMeta,children:[(0,l.jsxs)(c.Tag,{children:["ID: ",m.id]}),(0,l.jsx)(c.Tag,{color:"blue",children:C(m.class)})]}),(0,l.jsx)(k,{className:p.codeBlock,children:(0,l.jsx)("pre",{children:m.serialized||f("coreshop_messenger_no_data",{defaultValue:"No data available"})})})]})})]})},$=()=>{let{data:e,loading:r,error:s,reload:t}=function(){let[e,r]=(0,o.useState)([]),[s,t]=(0,o.useState)(!0),[a,l]=(0,o.useState)(null),n=(0,o.useCallback)(async()=>{try{t(!0),l(null);let e=await y.getMessageCount();r(e)}catch(e){l(e instanceof Error?e.message:"Failed to load chart data")}finally{t(!1)}},[]),i=_(n,500);return(0,o.useEffect)(()=>{n()},[n]),(0,o.useEffect)(()=>b.subscribe(e=>{console.debug("useMessengerChart: Received Mercure event, triggering reload"),i()}),[i]),{data:e,loading:s,error:a,reload:n}}(),{styles:a}=E(),{t:n}=(0,h.useTranslation)(),[i,u]=o.useState("pending"),[m,g]=o.useState(null),f=[{key:"pending",label:(0,l.jsxs)(c.Space,{size:4,children:[(0,l.jsx)(d.ClockCircleOutlined,{}),n("coreshop_messenger_pending_messages",{defaultValue:"Pending Messages"})]}),children:(0,l.jsx)("div",{className:a.tabContent,children:(0,l.jsx)(V,{selectedReceiver:m})})},{key:"failed",label:(0,l.jsxs)(c.Space,{size:4,children:[(0,l.jsx)(d.WarningOutlined,{}),n("coreshop_messenger_failed_messages",{defaultValue:"Failed Messages"})]}),children:(0,l.jsx)("div",{className:a.tabContent,children:(0,l.jsx)(C,{})})}];return(0,l.jsxs)("div",{className:a.container,children:[(0,l.jsxs)("div",{className:a.header,children:[(0,l.jsx)("div",{className:a.title,children:n("coreshop_messenger",{defaultValue:"Messenger"})}),(0,l.jsx)(c.Button,{type:"primary",icon:(0,l.jsx)(d.ReloadOutlined,{}),onClick:()=>{t()},children:n("coreshop_messenger_reload_all",{defaultValue:"Reload"})})]}),(0,l.jsx)("div",{className:a.chartSection,children:(0,l.jsx)(p,{data:e,loading:r,error:s,onBarClick:e=>{g(e),u("pending")}})}),(0,l.jsx)("div",{className:a.tabsSection,children:(0,l.jsx)(c.Tabs,{activeKey:i,onChange:u,type:"card",items:f,className:a.tabs})})]})},E=(0,u.createStyles)(e=>{let{css:r,token:s}=e;return{container:r`
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
  `}}),T={onInit(){t.container.get(a.serviceIds.widgetManager).registerWidget({name:"coreshop-messenger-widget",component:$})}};class M{getId(){return this.handlerId}shouldHandle(e){return"update"===e.type&&!!e.payload&&"coreshop.messenger.update"===e.payload.eventType}async handleMessage(e){if(!e.payload)return;let r=e.payload;b.emit(r)}onRegister(){}onUnregister(){}constructor(){this.handlerId="coreshop-messenger-handler"}}let F={name:"coreshop-messenger",onInit(){try{let e=t.container.get(a.serviceIds.globalMessageBus),r=new M;e.registerHandler(r)}catch(e){console.warn("CoreShop MessengerBundle: Failed to register Mercure message handler",e)}},onStartup(e){let{moduleSystem:r}=e;r.registerModule(i),r.registerModule(T)}}},2989:function(e,r,s){s.d(r,{ZF:()=>l});var t=s(5168),a=s.n(t);s(2703);let l=e=>"string"==typeof e&&e.includes("\n")?a().createElement("div",null,...e.split("\n").map((e,r)=>a().createElement("div",{key:r},e))):e}}]);