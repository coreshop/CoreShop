/*! For license information please see __federation_expose_default_export.0ae99504.js.LICENSE.txt */
"use strict";(self["chunk_coreshopstudioform "]=self["chunk_coreshopstudioform "]||[]).push([["525"],{5414:function(e,t,r){r.d(t,{o:()=>l});class l{addDecorator(e,t){return this.decorators.push({name:e,decorator:t}),this}overrideDecorator(e,t){let r=this.decorators.findIndex(t=>t.name===e);return r>=0?this.decorators[r]={name:e,decorator:t}:this.addDecorator(e,t),this}getDecorator(e){var t;return null==(t=this.decorators.find(t=>t.name===e))?void 0:t.decorator}removeDecorator(e){return this.decorators=this.decorators.filter(t=>t.name!==e),this}getDecoratorNames(){return this.decorators.map(e=>e.name)}copy(){let e=new l(this.baseConfig);return e.decorators=[...this.decorators],e}build(e){let t={...this.baseConfig};for(let{decorator:r}of this.decorators)t=r(t,e);return t}getBaseConfig(){return{...this.baseConfig}}constructor(e){this.decorators=[],this.baseConfig=e}}},1948:function(e,t,r){r.r(t),r.d(t,{default:()=>ee});var l=r(2977),o=r(8963),i=r(3964),n=r(3476),a=r(9363),s=r(1710),c=r(1877),d=r(931),u=r(6671),p=r(8848),m=r(2485),h=r(6925),f=r(2855),b=r(5168),y=r.n(b),g=r(1660),v=r(3222),x=r(6366),A=r(2568),T=r(493),j=r(4299),k=r(9119),w=r(1688),S=r(5530),C=r(3110),_=r(2288),P=r(4888),D=r(5210);let F=e=>{var t;let{config:r,data:l,onChange:o,currentLocale:i,form:n}=e,[a]=j.A.useForm(),s=n??a,{t:c}=(0,D.useTranslation)(),d=y().useRef(void 0),u=y().useRef(void 0),p=y().useRef(void 0),m=y().useMemo(()=>r.fields.some(e=>e.localized),[r.fields]);y().useEffect(()=>{let e=null==l?void 0:l.id,t=void 0!==i;if(void 0===e&&!t){if(l===p.current||s.isFieldsTouched(!0))return;p.current=l,l&&s.setFieldsValue(l);return}let r=d.current!==e,o=u.current!==i;if((r||o)&&(d.current=e,u.current=i,l)){let e={...l};m&&i&&(e.translations=e.translations??{},e.translations[i]||(e.translations[i]={})),s.setFieldsValue(e)}},[l,null==l?void 0:l.id,i,s,m]);let h=y().useMemo(()=>r.sections?[...r.sections].sort((e,t)=>(e.order??999)-(t.order??999)):[],[r.sections]),b=y().useMemo(()=>r.tabs?[...r.tabs].sort((e,t)=>(e.order??999)-(t.order??999)):[],[r.tabs]),g=null==(t=b[0])?void 0:t.key,v=e=>{let t=e.component,r=e.label?c(e.label,{defaultValue:e.label}):void 0,l=r;e.localized&&i&&(l=(0,f.jsxs)("span",{style:{display:"flex",alignItems:"center",gap:6},children:[(0,f.jsx)(P.A,{style:{color:"var(--ant-color-primary)",fontSize:12}}),r,(0,f.jsx)(k.A,{color:"blue",style:{marginLeft:4,fontSize:10,lineHeight:"16px",padding:"0 4px"},children:i.toUpperCase()})]}));let o=e.tooltip?c(e.tooltip,{defaultValue:e.tooltip}):void 0,n=e.localized&&i?["translations",i,...Array.isArray(e.name)?e.name:[e.name]]:e.name,a=(0,f.jsx)(j.A.Item,{label:l,name:n,rules:e.rules,required:e.required,tooltip:o,hidden:e.hidden,valuePropName:e.valuePropName,children:(0,f.jsx)(t,{disabled:e.disabled,...e.componentProps??{}})},String(n));return e.wrapper?e.wrapper(a):e.span?(0,f.jsx)(w.A,{span:e.span,children:a},String(n)):a},x=(e,t)=>{let l=r.fields.filter(r=>r.section===e&&(!t||(r.tab??g)===t));return 0===l.length?null:r.columns&&r.columns>1?(0,f.jsx)(S.A,{gutter:16,children:l.map(e=>v(e))}):l.map(e=>v(e))},A=(e,t)=>{let r=x(e.key,t);if(!r)return null;let l=c(e.title,{defaultValue:e.title}),o=e.description?c(e.description,{defaultValue:e.description}):void 0;return e.collapsible?(0,f.jsx)(C.A,{defaultActiveKey:e.defaultCollapsed?[]:[e.key],style:{marginBottom:16},children:(0,f.jsxs)(C.A.Panel,{header:(0,f.jsxs)("div",{style:{display:"flex",alignItems:"center",gap:8},children:[e.icon,(0,f.jsx)("span",{children:l})]}),children:[o&&(0,f.jsx)("div",{style:{marginBottom:16,color:"var(--ant-color-text-secondary)"},children:o}),r]},e.key)},e.key):(0,f.jsxs)("div",{style:{marginBottom:24},children:[l&&(0,f.jsxs)("h3",{style:{marginBottom:16},children:[e.icon," ",l]}),o&&(0,f.jsx)("div",{style:{marginBottom:16,color:"var(--ant-color-text-secondary)"},children:o}),r]},e.key)};return(0,f.jsx)(j.A,{form:s,layout:r.layout??"vertical",initialValues:l,onValuesChange:e=>{o(e)},...r.formProps??{},children:b.length>0?(0,f.jsx)(_.A,{defaultActiveKey:g,items:b.map(e=>({key:e.key,label:c(e.title,{defaultValue:e.title}),children:(0,f.jsxs)(f.Fragment,{children:[h.map(t=>A(t,e.key)),x(void 0,e.key)]})}))}):(0,f.jsxs)(f.Fragment,{children:[h.map(e=>A(e)),x(void 0)]})})},N=e=>{var t;let{value:r,onChange:l,disabled:o,field:i,widgetRegistry:n}=e,a=Array.isArray(r)?r:[],s=y().useMemo(()=>Object.entries(i.prototypes??{}),[i.prototypes]),c=null==(t=s[0])?void 0:t[0],[u,p]=y().useState(c);return y().useEffect(()=>{!u&&c&&p(c)},[c,u]),(0,f.jsxs)("div",{style:{display:"flex",flexDirection:"column",gap:12},children:[a.map((e,t)=>{let r=(e=>{if(i.prototypes&&Object.keys(i.prototypes).length>0){let t="object"==typeof e&&null!==e?e.type:void 0;return"string"==typeof t&&i.prototypes[t]?i.prototypes[t]:i.prototypes[c??""]}return i.prototype})(e),s=r?{...(0,T.D)(r,n),formProps:{component:!1}}:null;return(0,f.jsx)(g.A,{size:"small",extra:(0,f.jsx)(v.Ay,{type:"text",danger:!0,icon:(0,f.jsx)(x.A,{}),onClick:()=>{l&&l(a.filter((e,r)=>r!==t))},disabled:o}),children:s?(0,f.jsx)(F,{config:s,data:e??{},onChange:e=>((e,t)=>{if(!l)return;let r=[...a],o=r[e]??{};r[e]={...o,...t},l(r)})(t,e)}):null},t)}),(0,f.jsxs)("div",{style:{display:"flex",gap:8},children:[s.length>1?(0,f.jsx)(d.A,{style:{minWidth:200},value:u,onChange:p,options:s.map(e=>{let[t]=e;return{value:t,label:t}}),disabled:o}):null,(0,f.jsx)(v.Ay,{icon:(0,f.jsx)(A.A,{}),onClick:()=>{if(l){if(s.length>0){let e=u??c;if(!e)return;l([...a,{type:e,configuration:{}}]);return}l([...a,{}])}},disabled:o||s.length>1&&!u,children:"Add"})]})]})};var I=r(1521),$=r(7592);let E=e=>{var t,r;let{value:l,onChange:o,disabled:i,field:n,widgetRegistry:a}=e,{t:s}=(0,D.useTranslation)(),c=null!=l&&"object"==typeof l&&!Array.isArray(l),d=c?Object.entries(l):(Array.isArray(l)?l:[]).map((e,t)=>[String(t),e]),u=d.map(e=>{let[,t]=e;return t}),p=d.map(e=>{let[t]=e;return t}),m=n.prototype,h=(null==(t=n.extra)?void 0:t.allow_add)??!0,b=(null==(r=n.extra)?void 0:r.allow_delete)??!0,g=y().useCallback(e=>{o&&(c?o(Object.fromEntries(e)):o(e.map(e=>{let[,t]=e;return t})))},[o,c]),T=y().useCallback((e,t,r)=>{var l;let o=[...d],i=(null==(l=o[e])?void 0:l[1])??{};o[e]=[p[e],{...i,[t]:r}],g(o)},[d,p,g]),j=y().useCallback(e=>{g(d.filter((t,r)=>r!==e))},[d,g]),k=y().useCallback(()=>{let e=c?String(Date.now()):String(d.length);g([...d,[e,{}]])},[d,g,c]),w=y().useMemo(()=>{if(!(null==m?void 0:m.fields))return[];let e=m.fields.map(e=>{var t;let r=a.resolve(e);if(!r||(null==(t=r.extra)?void 0:t.hidden))return null;let l=r.component,o=r.props??{},n=r.valuePropName??"value",c=e.label??e.name;return{title:s(c,{defaultValue:c}),dataIndex:e.name,key:e.name,render:(t,r,a)=>{if(e.disabled)return(0,f.jsx)("span",{children:null!=t?String(t):""});let s={...o,[n]:t??void 0,onChange:t=>{let r=(null==t?void 0:t.target)!==void 0?t.target.value:t;T(a,e.name,r)},disabled:i,style:{width:"100%",...o.style}};return(0,f.jsx)(l,{...s})}}}).filter(e=>null!=e);return b&&e.push({title:"",dataIndex:"__actions",key:"__actions",width:60,render:(e,t,r)=>(0,f.jsx)(I.A,{title:s("coreshop_delete_confirm",{defaultValue:"Delete?"}),onConfirm:()=>j(r),okText:s("coreshop_yes",{defaultValue:"Yes"}),cancelText:s("coreshop_no",{defaultValue:"No"}),children:(0,f.jsx)(v.Ay,{type:"text",icon:(0,f.jsx)(x.A,{}),danger:!0,disabled:i})})}),e},[m,a,T,j,i,b,s]);return(0,f.jsxs)("div",{style:{display:"flex",flexDirection:"column",gap:8},children:[(0,f.jsx)($.A,{columns:w,dataSource:u,pagination:!1,rowKey:(e,t)=>p[t]??String(t),size:"small"}),h&&(0,f.jsx)("div",{children:(0,f.jsx)(v.Ay,{icon:(0,f.jsx)(A.A,{}),onClick:k,disabled:i,children:s("coreshop_add",{defaultValue:"Add"})})})]})};var B=r(4344),M=r(4781),R=r(2638),V=r(9700),q=r(6),O=r(5414);let z=new Map,L=new Map,G=async e=>{let t=z.get(e);if(t)return t;let r=L.get(e);if(r)return r;let l=(async()=>{try{let t=await fetch(`/pimcore-studio/api/coreshop-studio-form/schema/${encodeURIComponent(e)}`);if(!t.ok)throw Error(`Failed to fetch form schema for "${e}": ${t.statusText}`);let r=await t.json();return z.set(e,r),r}catch(t){throw console.error(`[StudioForm] Failed to load schema for "${e}":`,t),t}finally{L.delete(e)}})();return L.set(e,l),l},U=e=>{let{blockPrefix:t,data:r,onChange:o,decorators:i,currentLocale:n,form:a,embedded:s=!1}=e,{builder:c,loading:d,error:u}=((e,t)=>{let[r,o]=y().useState(null),[i,n]=y().useState(!0),[a,s]=y().useState(null);return y().useEffect(()=>{let r=!1;return(async()=>{try{n(!0),s(null);let i=await G(e);if(r)return;let a=l.container.get(B.M),c=(0,T.D)(i,a),d=new O.o(c);if(t)for(let{name:e,decorator:r}of t)d.addDecorator(e,r);o(d)}catch(e){r||s(e instanceof Error?e:Error(String(e)))}finally{r||n(!1)}})(),()=>{r=!0}},[e]),{builder:r,loading:i,error:a}})(t,i);if(d)return(0,f.jsx)("div",{style:{display:"flex",justifyContent:"center",padding:24},children:(0,f.jsx)(V.A,{})});if(u)return(0,f.jsx)(q.A,{type:"error",message:"Failed to load form",description:u.message,showIcon:!0});if(!c)return(0,f.jsx)(q.A,{type:"warning",message:"No form configuration available",showIcon:!0});let p=c.build({data:r,locale:n}),m=s?{...p,formProps:{...p.formProps,component:!1}}:p,h=(0,f.jsx)(F,{config:m,data:r,onChange:o,currentLocale:n,form:a});return s?(0,f.jsx)("div",{className:"coreshop-schema-form-embedded ant-form ant-form-vertical",children:h}):h},{Text:H}=R.A,W=`final class BasicDemoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Name',
            ])
            ->add('active', CheckboxType::class, [
                'label' => 'Active',
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
            ])
        ;
    }

    public function getBlockPrefix(): string
    {
        return 'coreshop_demo_basic';
    }
}`,K=`final class ChoiceDemoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('status', ChoiceType::class, [
                'label' => 'Status (Select)',
                'choices' => [
                    'Draft' => 'draft',
                    'Published' => 'published',
                    'Archived' => 'archived',
                ],
            ])
            ->add('tags', ChoiceType::class, [
                'label' => 'Tags (Multi-Select)',
                'choices' => [
                    'Featured' => 'featured',
                    'Sale' => 'sale',
                    'New' => 'new',
                    'Bestseller' => 'bestseller',
                ],
                'multiple' => true,
            ])
            ->add('visibility', ChoiceType::class, [
                'label' => 'Visibility (Radio)',
                'choices' => [
                    'Public' => 'public',
                    'Private' => 'private',
                    'Unlisted' => 'unlisted',
                ],
                'expanded' => true,
            ])
            ->add('channels', ChoiceType::class, [
                'label' => 'Channels (Checkbox Group)',
                'choices' => [
                    'Web' => 'web',
                    'Mobile' => 'mobile',
                    'POS' => 'pos',
                    'API' => 'api',
                ],
                'multiple' => true,
                'expanded' => true,
            ])
        ;
    }

    public function getBlockPrefix(): string
    {
        return 'coreshop_demo_choices';
    }
}`,Y=`final class FieldTypesDemoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, ['label' => 'Email'])
            ->add('url', UrlType::class, ['label' => 'URL'])
            ->add('password', PasswordType::class, ['label' => 'Password'])
            ->add('number', NumberType::class, ['label' => 'Number (scale=2)', 'scale' => 2])
            ->add('integer', IntegerType::class, ['label' => 'Integer'])
            ->add('date', DateType::class, ['label' => 'Date', 'widget' => 'single_text'])
            ->add('datetime', DateTimeType::class, ['label' => 'DateTime', 'widget' => 'single_text'])
            ->add('time', TimeType::class, ['label' => 'Time', 'widget' => 'single_text'])
            ->add('color', ColorType::class, ['label' => 'Color'])
            ->add('range', RangeType::class, ['label' => 'Range'])
        ;
    }
}

// Schema Enricher groups fields into collapsible sections:
final class FieldTypesDemoSchemaEnricher implements FormSchemaEnricherInterface
{
    private const array FIELD_SECTIONS = [
        'email' => 'text_inputs', 'url' => 'text_inputs', 'password' => 'text_inputs',
        'number' => 'numeric_date', 'integer' => 'numeric_date',
        'date' => 'numeric_date', 'datetime' => 'numeric_date', 'time' => 'numeric_date',
        'color' => 'numeric_date', 'range' => 'numeric_date',
    ];

    public function enrich(FormSchema $schema, string $formTypeClass): FormSchema
    {
        $schema->sections = [
            new SectionSchema('text_inputs', 'Text Inputs', 100, true, false),
            new SectionSchema('numeric_date', 'Numeric & Date', 90, true, true),
        ];

        foreach ($schema->fields as $field) {
            if (isset(self::FIELD_SECTIONS[$field->name])) {
                $field->section = self::FIELD_SECTIONS[$field->name];
            }
        }

        return $schema;
    }
}`,J=`final class CollectionEntryDemoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, ['label' => 'Name'])
            ->add('email', EmailType::class, ['label' => 'Email'])
            ->add('active', CheckboxType::class, ['label' => 'Active'])
        ;
    }

    public function getBlockPrefix(): string
    {
        return 'coreshop_demo_collection_entry';
    }
}

final class CollectionDemoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('groupName', TextType::class, [
                'label' => 'Group Name',
            ])
            ->add('members', CollectionType::class, [
                'label' => 'Members',
                'entry_type' => CollectionEntryDemoType::class,
                'allow_add' => true,
                'allow_delete' => true,
            ])
        ;
    }

    public function getBlockPrefix(): string
    {
        return 'coreshop_demo_collection';
    }
}`,Q=e=>{let{blockPrefix:t,description:r,phpSource:l}=e,[o,i]=y().useState({}),n=y().useCallback(e=>{i(t=>({...t,...e}))},[]);return(0,f.jsxs)("div",{children:[(0,f.jsx)(H,{type:"secondary",style:{display:"block",marginBottom:16},children:r}),(0,f.jsx)(U,{blockPrefix:t,data:o,onChange:n}),(0,f.jsx)(C.A,{style:{marginTop:16},items:[{key:"php",label:"PHP Source",children:(0,f.jsx)("pre",{style:{margin:0,fontSize:12,maxHeight:400,overflow:"auto",background:"#f5f5f5",padding:12,borderRadius:4},children:l})},{key:"state",label:"Current State (JSON)",children:(0,f.jsx)("pre",{style:{margin:0,fontSize:12,maxHeight:300,overflow:"auto"},children:JSON.stringify(o,null,2)})}]})]})},X=()=>{let e=[{key:"basic",label:"Basic",blockPrefix:"coreshop_demo_basic",description:"Minimal form: TextType, CheckboxType, TextareaType.",phpSource:W},{key:"choices",label:"Choices",blockPrefix:"coreshop_demo_choices",description:"All 4 ChoiceType variants: Select, Multi-Select, Radio.Group, Checkbox.Group.",phpSource:K},{key:"field-types",label:"Field Types",blockPrefix:"coreshop_demo_field_types",description:"All supported field types with collapsible sections: Email, URL, Password, Number, Integer, Date, DateTime, Time, Color, Range.",phpSource:Y},{key:"collection",label:"Collection",blockPrefix:"coreshop_demo_collection",description:"Dynamic list with compound sub-forms: each entry has Name, Email, Active fields. Add/remove items dynamically.",phpSource:J},...window.coreshopStudioFormDemoTabs??[]].map(e=>({key:e.key,label:e.label,children:(0,f.jsx)(Q,{blockPrefix:e.blockPrefix,description:e.description,phpSource:e.phpSource})}));return(0,f.jsx)(g.A,{title:"StudioFormBundle Demos",style:{margin:16},children:(0,f.jsx)(_.A,{items:e})})},Z={onInit(){l.container.get(M.serviceIds.widgetManager).registerWidget({name:"coreshop-studio-form-demos",component:X})}},ee={name:"coreshop-studio-form-plugin",onInit(){var e;l.container.bind(B.M).to(o.Y).inSingletonScope(),(e=l.container.get(B.M)).register("text",()=>({component:i.A})),e.register("textarea",()=>({component:i.A.TextArea,props:{rows:4}})),e.register("integer",()=>({component:n.A,props:{style:{width:"100%"},precision:0}})),e.register("number",()=>({component:n.A,props:{style:{width:"100%"}}})),e.register("checkbox",()=>({component:a.A,valuePropName:"checked"})),e.register("choice",e=>{let t=(e.choices??[]).map(e=>({value:e.value,label:e.label,group:e.group}));if(e.expanded)return e.multiple?{component:s.A.Group,props:{options:t.map(e=>{let{value:t,label:r}=e;return{value:t,label:r}})}}:{component:c.A.Group,props:{options:t.map(e=>{let{value:t,label:r}=e;return{value:t,label:r}}),optionType:"default"}};let r=new Map,l=[];for(let e of t){if(!e.group){l.push({value:e.value,label:e.label});continue}let t=r.get(e.group)??[];t.push({value:e.value,label:e.label}),r.set(e.group,t)}for(let[e,t]of r.entries())l.push({label:e,options:t});return{component:d.A,props:{options:l,allowClear:!0,showSearch:!0,filterOption:(e,t)=>((null==t?void 0:t.label)??"").toLowerCase().includes(e.toLowerCase()),mode:e.multiple?"multiple":void 0}}}),e.register("tag_collection",()=>({component:d.A,props:{mode:"tags",style:{width:"100%"},tokenSeparators:[","],open:!1}})),e.register("grid_collection",t=>({component:E,props:{field:t,widgetRegistry:e}})),e.register("collection",t=>({component:N,props:{field:t,widgetRegistry:e}})),e.register("hidden",()=>({component:i.A,extra:{hidden:!0}})),e.register("email",()=>({component:i.A,props:{type:"email"}})),e.register("url",()=>({component:i.A,props:{type:"url"}})),e.register("password",()=>({component:i.A.Password})),e.register("date",()=>({component:u.A,props:{style:{width:"100%"}}})),e.register("datetime",()=>({component:u.A,props:{showTime:!0,style:{width:"100%"}}})),e.register("time",()=>({component:p.A,props:{style:{width:"100%"}}})),e.register("color",()=>({component:m.A})),e.register("range",()=>({component:h.A}))},onStartup(e){let{moduleSystem:t}=e;t.registerModule(Z)}}},493:function(e,t,r){r.d(t,{D:()=>o});var l=r(3964);let o=(e,t)=>{let r=[],l=[],o=[];for(let t of e.sections)l.push({key:t.key,title:t.label,order:t.order,collapsible:t.collapsible,defaultCollapsed:t.defaultCollapsed});for(let t of e.tabs)o.push({key:t.key,title:t.label,order:t.order});for(let l of e.fields){let e=i(l,t);e&&r.push(...e)}return{fields:r,sections:l.length>0?l:void 0,tabs:o.length>0?o:void 0}},i=(e,t)=>{let r=d(e.section),o=d(e.tab),i=t.resolve(e);if(i){let{rules:t,componentProps:l}=s(e,i.props);return[{name:e.name,label:e.label??e.name,component:i.component,required:e.required,rules:t.length>0?t:void 0,componentProps:l,valuePropName:i.valuePropName,section:r,tab:o,...i.extra??{}}]}if(e.blockPrefixes.includes("coreshop_translations")&&e.children)return n(e,t);if(e.children&&e.children.fields.length>0)return a(e,t);let{rules:c,componentProps:u}=s(e);return[{name:e.name,label:e.label??e.name,component:l.A,required:e.required,rules:c.length>0?c:void 0,componentProps:u,section:r,tab:o}]},n=(e,t)=>{let r=[];if(!e.children)return r;let o=e.children.fields.find(e=>e.children&&e.children.fields.length>0);if(null==o?void 0:o.children){for(let i of o.children.fields){let o=t.resolve(i),{rules:n,componentProps:a}=s(i,null==o?void 0:o.props);r.push({name:i.name,label:i.label??i.name,component:(null==o?void 0:o.component)??l.A,required:i.required,localized:!0,rules:n.length>0?n:void 0,componentProps:a,valuePropName:null==o?void 0:o.valuePropName,section:d(e.section),tab:d(e.tab),...(null==o?void 0:o.extra)??{}})}return r}for(let o of e.children.fields){if(o.children&&o.children.fields.length>0)continue;let i=t.resolve(o),{rules:n,componentProps:a}=s(o,null==i?void 0:i.props);r.push({name:o.name,label:o.label??o.name,component:(null==i?void 0:i.component)??l.A,required:o.required,localized:!0,rules:n.length>0?n:void 0,componentProps:a,valuePropName:null==i?void 0:i.valuePropName,section:d(e.section),tab:d(e.tab),...(null==i?void 0:i.extra)??{}})}return r},a=(e,t)=>{let r=[];if(!e.children)return r;for(let o of e.children.fields){if(o.children&&o.children.fields.length>0){for(let l of a(o,t)){let t=Array.isArray(l.name)?l.name:[l.name];r.push({...l,name:[e.name,...t],section:d(e.section)??d(l.section),tab:d(e.tab)??d(l.tab)})}continue}let i=t.resolve(o),{rules:n,componentProps:c}=s(o,null==i?void 0:i.props);r.push({name:[e.name,o.name],label:o.label??o.name,component:(null==i?void 0:i.component)??l.A,required:o.required,rules:n.length>0?n:void 0,componentProps:c,valuePropName:null==i?void 0:i.valuePropName,section:d(e.section)??d(o.section),tab:d(e.tab)??d(o.tab),...(null==i?void 0:i.extra)??{}})}return r},s=(e,t)=>{let r=e.extra??{},l=r.attr&&"object"==typeof r.attr?r.attr:{},o=[];e.required&&o.push({required:!0}),"number"==typeof l.minlength&&o.push({min:l.minlength}),"number"==typeof l.maxlength&&o.push({max:l.maxlength});let i=c(l.min);null!==i&&o.push({type:"number",min:i});let n=c(l.max);if(null!==n&&o.push({type:"number",max:n}),"string"==typeof l.pattern&&""!==l.pattern)try{o.push({pattern:new RegExp(l.pattern)})}catch{}let a={...t??{},..."string"==typeof l.placeholder?{placeholder:l.placeholder}:{}};return{rules:o,componentProps:Object.keys(a).length>0?a:void 0}},c=e=>{if("number"==typeof e&&Number.isFinite(e))return e;if("string"==typeof e&&""!==e.trim()){let t=Number(e);if(Number.isFinite(t))return t}return null},d=e=>e??void 0},8963:function(e,t,r){r.d(t,{Y:()=>l});class l{register(e,t){this.resolvers.set(e,t)}get(e){return this.resolvers.get(e)}has(e){return this.resolvers.has(e)}resolve(e){let t=e.blockPrefixes;for(let r=t.length-1;r>=0;r--){let l=this.resolvers.get(t[r]);if(l){let t=l(e);if(t)return t}}return null}getRegisteredTypes(){return Array.from(this.resolvers.keys())}constructor(){this.resolvers=new Map}}},4344:function(e,t,r){r.d(t,{M:()=>l});let l="CoreShop/StudioForm/WidgetRegistry"}}]);