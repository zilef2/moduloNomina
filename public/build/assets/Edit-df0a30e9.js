import{p as $,T as x,m as C,f as u,a as o,w as f,o as g,b as t,t as m,u as i,v as V,A as j,d as v,n as B,e as E,y as N,B as U}from"./app-6f12aaad.js";import{_}from"./InputError-28dc37f5.js";import{_ as d}from"./InputLabel-e0cca7fa.js";import{_ as A,a as D}from"./SecondaryButton-eab9626f.js";import{_ as F}from"./PrimaryButton-3b5071cd.js";import{_ as M}from"./TextInput-a8b78078.js";import{_ as O}from"./Checkbox-bd91b4cf.js";const T={class:"space-y-6"},q={class:"text-lg font-medium text-gray-900 dark:text-gray-100"},z={class:"my-6 space-y-4"},L={class:"flex justify-start items-center space-x-2 mt-2"},G={class:"grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-2 mt-2"},H=["id","value"],I={class:"flex justify-end"},Y={__name:"Edit",props:{show:Boolean,title:String,role:Object,permissions:Object},emits:["close"],setup(y,{emit:b}){const r=y,n=$({multipleSelect:!1}),c=b,e=x({name:"",permissions:[]}),h=()=>{var s;e.put(route("role.update",(s=r.role)==null?void 0:s.id),{preserveScroll:!0,onSuccess:()=>{c("close"),e.reset(),n.multipleSelect=!1},onError:()=>null,onFinish:()=>null})};C(()=>{var s,l,a;r.show&&(e.errors={},e.name=(s=r.role)==null?void 0:s.name,e.permissions=(l=r.role.permissions)==null?void 0:l.map(p=>p.id)),r.permissions.length==((a=r.role)==null?void 0:a.permissions.length)?n.multipleSelect=!0:n.multipleSelect=!1});const k=s=>{s.target.checked===!1?e.permissions=[]:(e.permissions=[],r.permissions.forEach(l=>{e.permissions.push(l.id)}))},S=()=>{r.permissions.length==e.permissions.length?n.multipleSelect=!0:n.multipleSelect=!1};return(s,l)=>(g(),u("section",T,[o(A,{show:r.show,onClose:l[4]||(l[4]=a=>c("close"))},{default:f(()=>[t("form",{class:"p-6",onSubmit:E(h,["prevent"])},[t("h2",q,m(s.lang().label.edit)+" "+m(r.title),1),t("div",z,[t("div",null,[o(d,{for:"name",value:s.lang().label.role},null,8,["value"]),o(M,{id:"name",type:"text",class:"mt-1 block w-full",modelValue:i(e).name,"onUpdate:modelValue":l[0]||(l[0]=a=>i(e).name=a),required:"",placeholder:s.lang().placeholder.name,error:i(e).errors.name},null,8,["modelValue","placeholder","error"]),o(_,{class:"mt-2",message:i(e).errors.name},null,8,["message"])]),t("div",null,[o(d,{value:s.lang().label.permission},null,8,["value"]),o(_,{class:"mt-2",message:i(e).errors.permissions},null,8,["message"]),t("div",L,[o(O,{id:"permission-all",checked:n.multipleSelect,"onUpdate:checked":l[1]||(l[1]=a=>n.multipleSelect=a),onChange:k},null,8,["checked"]),o(d,{for:"permission-all",value:s.lang().label.check_all},null,8,["value"])]),t("div",G,[(g(!0),u(V,null,j(r.permissions,(a,p)=>(g(),u("div",{class:"flex items-center justify-start space-x-2",key:p},[N(t("input",{onChange:S,class:"rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-primary dark:text-primary shadow-sm focus:ring-primary/80 dark:focus:ring-primary dark:focus:ring-offset-gray-800 dark:checked:bg-primary dark:checked:border-primary",type:"checkbox",id:"permission_"+a.id,value:a.id,"onUpdate:modelValue":l[2]||(l[2]=w=>i(e).permissions=w)},null,40,H),[[U,i(e).permissions]]),o(d,{for:"permission_"+a.id,value:s.lang().permissions[a.name]},null,8,["for","value"])]))),128))])])]),t("div",I,[o(D,{disabled:i(e).processing,onClick:l[3]||(l[3]=a=>c("close"))},{default:f(()=>[v(m(s.lang().button.close),1)]),_:1},8,["disabled"]),o(F,{class:B(["ml-3",{"opacity-25":i(e).processing}]),disabled:i(e).processing,onClick:h},{default:f(()=>[v(m(i(e).processing?s.lang().button.save+"...":s.lang().button.save),1)]),_:1},8,["class","disabled"])])],32)]),_:1},8,["show"])]))}};export{Y as default};