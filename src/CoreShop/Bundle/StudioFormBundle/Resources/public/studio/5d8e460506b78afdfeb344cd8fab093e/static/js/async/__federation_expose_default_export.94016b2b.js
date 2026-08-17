/*! For license information please see __federation_expose_default_export.94016b2b.js.LICENSE.txt */
"use strict";(self["chunk_coreshopstudioform "]=self["chunk_coreshopstudioform "]||[]).push([["525"],{5414:function(e,t,r){r.d(t,{o:()=>l});class l{addDecorator(e,t){return this.decorators.push({name:e,decorator:t}),this}overrideDecorator(e,t){let r=this.decorators.findIndex(t=>t.name===e);return r>=0?this.decorators[r]={name:e,decorator:t}:this.addDecorator(e,t),this}getDecorator(e){var t;return null==(t=this.decorators.find(t=>t.name===e))?void 0:t.decorator}removeDecorator(e){return this.decorators=this.decorators.filter(t=>t.name!==e),this}getDecoratorNames(){return this.decorators.map(e=>e.name)}copy(){let e=new l(this.baseConfig);return e.decorators=[...this.decorators],e}build(e){let t={...this.baseConfig};for(let{decorator:r}of this.decorators)t=r(t,e);return t}getBaseConfig(){return{...this.baseConfig}}constructor(e){this.decorators=[],this.baseConfig=e}}},5675:function(e,t,r){r.r(t),r.d(t,{default:()=>B});var l=r(2977),o=r(8963),i=r(8701),n=r(2855),a=r(5168),s=r.n(a),c=r(3033);let d=(e,t)=>{let r=[],l=[],o=[];for(let t of e.sections)l.push({key:t.key,title:t.label,order:t.order,collapsible:t.collapsible,defaultCollapsed:t.defaultCollapsed});for(let t of e.tabs)o.push({key:t.key,title:t.label,order:t.order});for(let l of e.fields){let e=u(l,t);e&&r.push(...e)}return{fields:r,sections:l.length>0?l:void 0,tabs:o.length>0?o:void 0}},u=(e,t)=>{let r=b(e.section),l=b(e.tab),o=t.resolve(e);if(o){let{rules:t,componentProps:i}=h(e,o.props);return[{name:e.name,label:e.label??e.name,component:o.component,required:e.required,rules:t.length>0?t:void 0,componentProps:i,valuePropName:o.valuePropName,section:r,tab:l,...o.extra??{}}]}if(e.blockPrefixes.includes("coreshop_translations")&&e.children)return p(e,t);if(e.children&&e.children.fields.length>0)return m(e,t);let{rules:n,componentProps:a}=h(e);return[{name:e.name,label:e.label??e.name,component:i.Input,required:e.required,rules:n.length>0?n:void 0,componentProps:a,section:r,tab:l}]},p=(e,t)=>{let r=[];if(!e.children)return r;let l=e.children.fields.find(e=>e.children&&e.children.fields.length>0);if(null==l?void 0:l.children){for(let o of l.children.fields){let l=t.resolve(o),{rules:n,componentProps:a}=h(o,null==l?void 0:l.props);r.push({name:o.name,label:o.label??o.name,component:(null==l?void 0:l.component)??i.Input,required:o.required,localized:!0,rules:n.length>0?n:void 0,componentProps:a,valuePropName:null==l?void 0:l.valuePropName,section:b(e.section),tab:b(e.tab),...(null==l?void 0:l.extra)??{}})}return r}for(let l of e.children.fields){if(l.children&&l.children.fields.length>0)continue;let o=t.resolve(l),{rules:n,componentProps:a}=h(l,null==o?void 0:o.props);r.push({name:l.name,label:l.label??l.name,component:(null==o?void 0:o.component)??i.Input,required:l.required,localized:!0,rules:n.length>0?n:void 0,componentProps:a,valuePropName:null==o?void 0:o.valuePropName,section:b(e.section),tab:b(e.tab),...(null==o?void 0:o.extra)??{}})}return r},m=(e,t)=>{let r=[];if(!e.children)return r;for(let l of e.children.fields){if(l.children&&l.children.fields.length>0){for(let o of m(l,t)){let t=Array.isArray(o.name)?o.name:[o.name];r.push({...o,name:[e.name,...t],section:b(e.section)??b(o.section),tab:b(e.tab)??b(o.tab)})}continue}let o=t.resolve(l),{rules:n,componentProps:a}=h(l,null==o?void 0:o.props);r.push({name:[e.name,l.name],label:l.label??l.name,component:(null==o?void 0:o.component)??i.Input,required:l.required,rules:n.length>0?n:void 0,componentProps:a,valuePropName:null==o?void 0:o.valuePropName,section:b(e.section)??b(l.section),tab:b(e.tab)??b(l.tab),...(null==o?void 0:o.extra)??{}})}return r},h=(e,t)=>{let r=e.extra??{},l=r.attr&&"object"==typeof r.attr?r.attr:{},o=[];e.required&&o.push({required:!0}),"number"==typeof l.minlength&&o.push({min:l.minlength}),"number"==typeof l.maxlength&&o.push({max:l.maxlength});let i=f(l.min);null!==i&&o.push({type:"number",min:i});let n=f(l.max);if(null!==n&&o.push({type:"number",max:n}),"string"==typeof l.pattern&&""!==l.pattern)try{o.push({pattern:new RegExp(l.pattern)})}catch{}let a={...t??{},..."string"==typeof l.placeholder?{placeholder:l.placeholder}:{}};return{rules:o,componentProps:Object.keys(a).length>0?a:void 0}},f=e=>{if("number"==typeof e&&Number.isFinite(e))return e;if("string"==typeof e&&""!==e.trim()){let t=Number(e);if(Number.isFinite(t))return t}return null},b=e=>e??void 0;var y=r(5210);let g=e=>{var t;let{config:r,data:l,onChange:o,currentLocale:a,form:d}=e,[u]=i.Form.useForm(),p=d??u,{t:m}=(0,y.useTranslation)(),h=s().useRef(void 0),f=s().useRef(void 0),b=s().useRef(void 0),g=s().useMemo(()=>r.fields.some(e=>e.localized),[r.fields]);s().useEffect(()=>{let e=null==l?void 0:l.id,t=void 0!==a;if(void 0===e&&!t){if(l===b.current||p.isFieldsTouched(!0))return;b.current=l,l&&p.setFieldsValue(l);return}let r=h.current!==e,o=f.current!==a;if((r||o)&&(h.current=e,f.current=a,l)){let e={...l};g&&a&&(e.translations=e.translations??{},e.translations[a]||(e.translations[a]={})),p.setFieldsValue(e)}},[l,null==l?void 0:l.id,a,p,g]);let x=s().useMemo(()=>r.sections?[...r.sections].sort((e,t)=>(e.order??999)-(t.order??999)):[],[r.sections]),v=s().useMemo(()=>r.tabs?[...r.tabs].sort((e,t)=>(e.order??999)-(t.order??999)):[],[r.tabs]),T=null==(t=v[0])?void 0:t.key,k=e=>{let t=e.component,r=e.label?m(e.label,{defaultValue:e.label}):void 0,l=r;e.localized&&a&&(l=(0,n.jsxs)("span",{style:{display:"flex",alignItems:"center",gap:6},children:[(0,n.jsx)(c.GlobalOutlined,{style:{color:"var(--ant-color-primary)",fontSize:12}}),r,(0,n.jsx)(i.Tag,{color:"blue",style:{marginLeft:4,fontSize:10,lineHeight:"16px",padding:"0 4px"},children:a.toUpperCase()})]}));let o=e.tooltip?m(e.tooltip,{defaultValue:e.tooltip}):void 0,s=e.localized&&a?["translations",a,...Array.isArray(e.name)?e.name:[e.name]]:e.name,d=(0,n.jsx)(i.Form.Item,{label:l,name:s,rules:e.rules,required:e.required,tooltip:o,hidden:e.hidden,valuePropName:e.valuePropName,children:(0,n.jsx)(t,{disabled:e.disabled,...e.componentProps??{}})},String(s));return e.wrapper?e.wrapper(d):e.span?(0,n.jsx)(i.Col,{span:e.span,children:d},String(s)):d},S=(e,t)=>{let l=r.fields.filter(r=>r.section===e&&(!t||(r.tab??T)===t));return 0===l.length?null:r.columns&&r.columns>1?(0,n.jsx)(i.Row,{gutter:16,children:l.map(e=>k(e))}):l.map(e=>k(e))},C=(e,t)=>{let r=S(e.key,t);if(!r)return null;let l=m(e.title,{defaultValue:e.title}),o=e.description?m(e.description,{defaultValue:e.description}):void 0;return e.collapsible?(0,n.jsx)(i.Collapse,{defaultActiveKey:e.defaultCollapsed?[]:[e.key],style:{marginBottom:16},children:(0,n.jsxs)(i.Collapse.Panel,{header:(0,n.jsxs)("div",{style:{display:"flex",alignItems:"center",gap:8},children:[e.icon,(0,n.jsx)("span",{children:l})]}),children:[o&&(0,n.jsx)("div",{style:{marginBottom:16,color:"var(--ant-color-text-secondary)"},children:o}),r]},e.key)},e.key):(0,n.jsxs)("div",{style:{marginBottom:24},children:[l&&(0,n.jsxs)("h3",{style:{marginBottom:16},children:[e.icon," ",l]}),o&&(0,n.jsx)("div",{style:{marginBottom:16,color:"var(--ant-color-text-secondary)"},children:o}),r]},e.key)};return(0,n.jsx)(i.Form,{form:p,layout:r.layout??"vertical",initialValues:l,onValuesChange:e=>{o(e)},...r.formProps??{},children:v.length>0?(0,n.jsx)(i.Tabs,{defaultActiveKey:T,items:v.map(e=>({key:e.key,label:m(e.title,{defaultValue:e.title}),children:(0,n.jsxs)(n.Fragment,{children:[x.map(t=>C(t,e.key)),S(void 0,e.key)]})}))}):(0,n.jsxs)(n.Fragment,{children:[x.map(e=>C(e)),S(void 0)]})})},x=e=>{var t;let{value:r,onChange:l,disabled:o,field:a,widgetRegistry:u}=e,p=Array.isArray(r)?r:[],m=s().useMemo(()=>Object.entries(a.prototypes??{}),[a.prototypes]),h=null==(t=m[0])?void 0:t[0],[f,b]=s().useState(h);return s().useEffect(()=>{!f&&h&&b(h)},[h,f]),(0,n.jsxs)("div",{style:{display:"flex",flexDirection:"column",gap:12},children:[p.map((e,t)=>{let r=(e=>{if(a.prototypes&&Object.keys(a.prototypes).length>0){let t="object"==typeof e&&null!==e?e.type:void 0;return"string"==typeof t&&a.prototypes[t]?a.prototypes[t]:a.prototypes[h??""]}return a.prototype})(e),s=r?{...d(r,u),formProps:{component:!1}}:null;return(0,n.jsx)(i.Card,{size:"small",extra:(0,n.jsx)(i.Button,{type:"text",danger:!0,icon:(0,n.jsx)(c.DeleteOutlined,{}),onClick:()=>{l&&l(p.filter((e,r)=>r!==t))},disabled:o}),children:s?(0,n.jsx)(g,{config:s,data:e??{},onChange:e=>((e,t)=>{if(!l)return;let r=[...p],o=r[e]??{};r[e]={...o,...t},l(r)})(t,e)}):null},t)}),(0,n.jsxs)("div",{style:{display:"flex",gap:8},children:[m.length>1?(0,n.jsx)(i.Select,{style:{minWidth:200},value:f,onChange:b,options:m.map(e=>{let[t]=e;return{value:t,label:t}}),disabled:o}):null,(0,n.jsx)(i.Button,{icon:(0,n.jsx)(c.PlusOutlined,{}),onClick:()=>{if(l){if(m.length>0){let e=f??h;if(!e)return;l([...p,{type:e,configuration:{}}]);return}l([...p,{}])}},disabled:o||m.length>1&&!f,children:"Add"})]})]})},v=e=>{var t,r;let{value:l,onChange:o,disabled:a,field:d,widgetRegistry:u}=e,{t:p}=(0,y.useTranslation)(),m=null!=l&&"object"==typeof l&&!Array.isArray(l),h=m?Object.entries(l):(Array.isArray(l)?l:[]).map((e,t)=>[String(t),e]),f=h.map(e=>{let[,t]=e;return t}),b=h.map(e=>{let[t]=e;return t}),g=d.prototype,x=(null==(t=d.extra)?void 0:t.allow_add)??!0,v=(null==(r=d.extra)?void 0:r.allow_delete)??!0,T=s().useCallback(e=>{o&&(m?o(Object.fromEntries(e)):o(e.map(e=>{let[,t]=e;return t})))},[o,m]),k=s().useCallback((e,t,r)=>{var l;let o=[...h],i=(null==(l=o[e])?void 0:l[1])??{};o[e]=[b[e],{...i,[t]:r}],T(o)},[h,b,T]),S=s().useCallback(e=>{T(h.filter((t,r)=>r!==e))},[h,T]),C=s().useCallback(()=>{let e=m?String(Date.now()):String(h.length);T([...h,[e,{}]])},[h,T,m]),j=s().useMemo(()=>{if(!(null==g?void 0:g.fields))return[];let e=g.fields.map(e=>{var t;let r=u.resolve(e);if(!r||(null==(t=r.extra)?void 0:t.hidden))return null;let l=r.component,o=r.props??{},i=r.valuePropName??"value",s=e.label??e.name;return{title:p(s,{defaultValue:s}),dataIndex:e.name,key:e.name,render:(t,r,s)=>{if(e.disabled)return(0,n.jsx)("span",{children:null!=t?String(t):""});let c={...o,[i]:t??void 0,onChange:t=>{let r=(null==t?void 0:t.target)!==void 0?t.target.value:t;k(s,e.name,r)},disabled:a,style:{width:"100%",...o.style}};return(0,n.jsx)(l,{...c})}}}).filter(e=>null!=e);return v&&e.push({title:"",dataIndex:"__actions",key:"__actions",width:60,render:(e,t,r)=>(0,n.jsx)(i.Popconfirm,{title:p("coreshop_delete_confirm",{defaultValue:"Delete?"}),onConfirm:()=>S(r),okText:p("coreshop_yes",{defaultValue:"Yes"}),cancelText:p("coreshop_no",{defaultValue:"No"}),children:(0,n.jsx)(i.Button,{type:"text",icon:(0,n.jsx)(c.DeleteOutlined,{}),danger:!0,disabled:a})})}),e},[g,u,k,S,a,v,p]);return(0,n.jsxs)("div",{style:{display:"flex",flexDirection:"column",gap:8},children:[(0,n.jsx)(i.Table,{columns:j,dataSource:f,pagination:!1,rowKey:(e,t)=>b[t]??String(t),size:"small"}),x&&(0,n.jsx)("div",{children:(0,n.jsx)(i.Button,{icon:(0,n.jsx)(c.PlusOutlined,{}),onClick:C,disabled:a,children:p("coreshop_add",{defaultValue:"Add"})})})]})};var T=r(4344),k=r(4781),S=r(5414);let C=new Map,j=new Map,w=async e=>{let t=C.get(e);if(t)return t;let r=j.get(e);if(r)return r;let l=(async()=>{try{let t=await fetch(`/pimcore-studio/api/coreshop-studio-form/schema/${encodeURIComponent(e)}`);if(!t.ok)throw Error(`Failed to fetch form schema for "${e}": ${t.statusText}`);let r=await t.json();return C.set(e,r),r}catch(t){throw console.error(`[StudioForm] Failed to load schema for "${e}":`,t),t}finally{j.delete(e)}})();return j.set(e,l),l},_=e=>{let{blockPrefix:t,data:r,onChange:o,decorators:a,currentLocale:c,form:u,embedded:p=!1}=e,{builder:m,loading:h,error:f}=((e,t)=>{let[r,o]=s().useState(null),[i,n]=s().useState(!0),[a,c]=s().useState(null);return s().useEffect(()=>{let r=!1;return(async()=>{try{n(!0),c(null);let i=await w(e);if(r)return;let a=l.container.get(T.M),s=d(i,a),u=new S.o(s);if(t)for(let{name:e,decorator:r}of t)u.addDecorator(e,r);o(u)}catch(e){r||c(e instanceof Error?e:Error(String(e)))}finally{r||n(!1)}})(),()=>{r=!0}},[e]),{builder:r,loading:i,error:a}})(t,a);if(h)return(0,n.jsx)("div",{style:{display:"flex",justifyContent:"center",padding:24},children:(0,n.jsx)(i.Spin,{})});if(f)return(0,n.jsx)(i.Alert,{type:"error",message:"Failed to load form",description:f.message,showIcon:!0});if(!m)return(0,n.jsx)(i.Alert,{type:"warning",message:"No form configuration available",showIcon:!0});let b=m.build({data:r,locale:c}),y=p?{...b,formProps:{...b.formProps,component:!1}}:b,x=(0,n.jsx)(g,{config:y,data:r,onChange:o,currentLocale:c,form:u});return p?(0,n.jsx)("div",{className:"coreshop-schema-form-embedded ant-form ant-form-vertical",children:x}):x},{Text:P}=i.Typography,I=`final class BasicDemoType extends AbstractType
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
}`,F=`final class ChoiceDemoType extends AbstractType
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
}`,D=`final class FieldTypesDemoType extends AbstractType
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
}`,N=`final class CollectionEntryDemoType extends AbstractType
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
}`,A=e=>{let{blockPrefix:t,description:r,phpSource:l}=e,[o,a]=s().useState({}),c=s().useCallback(e=>{a(t=>({...t,...e}))},[]);return(0,n.jsxs)("div",{children:[(0,n.jsx)(P,{type:"secondary",style:{display:"block",marginBottom:16},children:r}),(0,n.jsx)(_,{blockPrefix:t,data:o,onChange:c}),(0,n.jsx)(i.Collapse,{style:{marginTop:16},items:[{key:"php",label:"PHP Source",children:(0,n.jsx)("pre",{style:{margin:0,fontSize:12,maxHeight:400,overflow:"auto",background:"#f5f5f5",padding:12,borderRadius:4},children:l})},{key:"state",label:"Current State (JSON)",children:(0,n.jsx)("pre",{style:{margin:0,fontSize:12,maxHeight:300,overflow:"auto"},children:JSON.stringify(o,null,2)})}]})]})},$=()=>{let e=[{key:"basic",label:"Basic",blockPrefix:"coreshop_demo_basic",description:"Minimal form: TextType, CheckboxType, TextareaType.",phpSource:I},{key:"choices",label:"Choices",blockPrefix:"coreshop_demo_choices",description:"All 4 ChoiceType variants: Select, Multi-Select, Radio.Group, Checkbox.Group.",phpSource:F},{key:"field-types",label:"Field Types",blockPrefix:"coreshop_demo_field_types",description:"All supported field types with collapsible sections: Email, URL, Password, Number, Integer, Date, DateTime, Time, Color, Range.",phpSource:D},{key:"collection",label:"Collection",blockPrefix:"coreshop_demo_collection",description:"Dynamic list with compound sub-forms: each entry has Name, Email, Active fields. Add/remove items dynamically.",phpSource:N},...window.coreshopStudioFormDemoTabs??[]].map(e=>({key:e.key,label:e.label,children:(0,n.jsx)(A,{blockPrefix:e.blockPrefix,description:e.description,phpSource:e.phpSource})}));return(0,n.jsx)(i.Card,{title:"StudioFormBundle Demos",style:{margin:16},children:(0,n.jsx)(i.Tabs,{items:e})})},E={onInit(){l.container.get(k.serviceIds.widgetManager).registerWidget({name:"coreshop-studio-form-demos",component:$})}},B={name:"coreshop-studio-form-plugin",priority:-1e3,onInit(){var e;l.container.bind(T.M).to(o.Y).inSingletonScope(),(e=l.container.get(T.M)).register("text",()=>({component:i.Input})),e.register("textarea",()=>({component:i.Input.TextArea,props:{rows:4}})),e.register("integer",()=>({component:i.InputNumber,props:{style:{width:"100%"},precision:0}})),e.register("number",()=>({component:i.InputNumber,props:{style:{width:"100%"}}})),e.register("checkbox",()=>({component:i.Switch,valuePropName:"checked"})),e.register("choice",e=>{let t=(e.choices??[]).map(e=>({value:e.value,label:e.label,group:e.group}));if(e.expanded)return e.multiple?{component:i.Checkbox.Group,props:{options:t.map(e=>{let{value:t,label:r}=e;return{value:t,label:r}})}}:{component:i.Radio.Group,props:{options:t.map(e=>{let{value:t,label:r}=e;return{value:t,label:r}}),optionType:"default"}};let r=new Map,l=[];for(let e of t){if(!e.group){l.push({value:e.value,label:e.label});continue}let t=r.get(e.group)??[];t.push({value:e.value,label:e.label}),r.set(e.group,t)}for(let[e,t]of r.entries())l.push({label:e,options:t});return{component:i.Select,props:{options:l,allowClear:!0,showSearch:!0,filterOption:(e,t)=>((null==t?void 0:t.label)??"").toLowerCase().includes(e.toLowerCase()),mode:e.multiple?"multiple":void 0}}}),e.register("tag_collection",()=>({component:i.Select,props:{mode:"tags",style:{width:"100%"},tokenSeparators:[","],open:!1}})),e.register("grid_collection",t=>({component:v,props:{field:t,widgetRegistry:e}})),e.register("collection",t=>({component:x,props:{field:t,widgetRegistry:e}})),e.register("hidden",()=>({component:i.Input,extra:{hidden:!0}})),e.register("email",()=>({component:i.Input,props:{type:"email"}})),e.register("url",()=>({component:i.Input,props:{type:"url"}})),e.register("password",()=>({component:i.Input.Password})),e.register("date",()=>({component:i.DatePicker,props:{style:{width:"100%"}}})),e.register("datetime",()=>({component:i.DatePicker,props:{showTime:!0,style:{width:"100%"}}})),e.register("time",()=>({component:i.TimePicker,props:{style:{width:"100%"}}})),e.register("color",()=>({component:i.ColorPicker})),e.register("range",()=>({component:i.Slider}))},onStartup(e){let{moduleSystem:t}=e;t.registerModule(E)}}},8963:function(e,t,r){r.d(t,{Y:()=>l});class l{register(e,t){this.resolvers.set(e,t)}get(e){return this.resolvers.get(e)}has(e){return this.resolvers.has(e)}resolve(e){let t=e.blockPrefixes;for(let r=t.length-1;r>=0;r--){let l=this.resolvers.get(t[r]);if(l){let t=l(e);if(t)return t}}return null}getRegisteredTypes(){return Array.from(this.resolvers.keys())}constructor(){this.resolvers=new Map}}},4344:function(e,t,r){r.d(t,{M:()=>l});let l="CoreShop/StudioForm/WidgetRegistry"}}]);