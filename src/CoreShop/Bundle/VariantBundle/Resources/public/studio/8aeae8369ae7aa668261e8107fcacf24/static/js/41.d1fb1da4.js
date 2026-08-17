/*! For license information please see 41.d1fb1da4.js.LICENSE.txt */
"use strict";(self["chunk_coreshopvariant "]=self["chunk_coreshopvariant "]||[]).push([["41"],{238:function(t,n,i){i(2855),i(5168),i(199),i(403)},403:function(t,n,i){i(2855),i(5168),i(8701)},891:function(t,n,i){var e=i(8203),o=i(6239);class s{async getConfig(){let t=`${this.basePath}/coreshop/resource/config`,n=await fetch(t,{credentials:"same-origin"});if(!n.ok)throw Error(`Config request failed: ${n.status}`);return await n.json()}constructor(t="/pimcore-studio/api"){this.basePath=t}}(0,e.Cg)([(0,o._G)(),(0,e.Sn)("design:type",Function),(0,e.Sn)("design:paramtypes",[])],class t{async getConfig(){return this.config?this.config:(this.loading||(this.loading=this.api.getConfig().then(t=>(this.config=t,this.loading=null,t))),this.loading)}async isClassAllowedForResource(t,n){let i=await this.getConfig(),e=t.split(".");if(2!==e.length)return!1;let[o,s]=e,a=i.stack[o];if(!a)return!1;let c=a[s];return!!c&&c.includes(n)}async getAllowedClasses(t){let n=await this.getConfig(),i=t.split(".");if(2!==i.length)return[];let[e,o]=i,s=n.stack[e];return s&&s[o]||[]}clearCache(){this.config=null,this.loading=null}constructor(){this.config=null,this.loading=null,this.api=new s}})},8268:function(t,n,i){i(2855),i(5168)},3877:function(t,n,i){i(2855),i(5168),i(2696),i(5210)},7350:function(t,n,i){i(2855),i(5168),i(5210),i(8701),i(2696),i(199),(0,i(6345).createStyles)(t=>{let{token:n,css:i}=t;return{tree:i`
    padding: ${n.paddingXS}px 0;

    .ant-tree-title__btn {
      height: 24px;
    }
  `,contentPadding:i`
    padding: ${n.paddingSM}px;
  `,inactive:i`
    .ant-tree-title__btn {
      color: ${n.colorTextDisabled};
    }
  `}})},9646:function(t,n,i){i(2855),i(5168),i(2696),i(7350),i(8268)},1670:function(t,n,i){i(2855),i(5168),i(2696),i(7350),i(2989)},7602:function(t,n,i){i(2855),i(5168),i(7350),i(2696),i(2989),i(3877),i(2703),i(8701),(0,i(6345).createStyles)(t=>{let{token:n,css:i}=t;return{contentPadding:i`
    padding: ${n.paddingSM}px;
  `,detailContent:i`
    overflow: auto;
  `}})},199:function(t,n,i){i(2855),i(5168),i(2696)},5579:function(t,n,i){i(2855),i(5168),i(2977)},3508:function(t,n,i){i(5168)},8385:function(t,n,i){i(5168)},6905:function(t,n,i){i(2977)}}]);