import{v as p,c as f,w as d,o as c,a as r,u as a,X as w,b as t,d as g,t as _,h,e as v}from"./app-74faa0bb.js";import{_ as b,a as V}from"./AuntheticationIllustration-c5272bfe.js";import{_ as n}from"./InputError-815f255c.js";import{_ as m}from"./InputLabel-7cdc2767.js";import{_ as i}from"./TextInput-c1c823a9.js";import"./index-79bb51ee.js";import"./SwitchLangNavbar-b2445a0d.js";const y=["onSubmit"],k={class:"mt-4"},$={class:"mt-4"},q={class:"mt-4"},U={class:"flex items-center justify-between mt-4"},R={__name:"Register",setup(B){const e=p({name:"",email:"",password:"",password_confirmation:"",terms:!1}),u=()=>{e.post(route("register"),{onFinish:()=>e.reset("password","password_confirmation")})};return(o,l)=>(c(),f(b,null,{illustration:d(()=>[r(V,{type:"login",class:"w-72 h-auto"})]),default:d(()=>[r(a(w),{title:o.lang().label.register},null,8,["title"]),t("form",{onSubmit:v(u,["prevent"])},[t("div",null,[r(m,{for:"name",value:o.lang().label.name},null,8,["value"]),r(i,{id:"name",type:"text",class:"mt-1 block w-full",modelValue:a(e).name,"onUpdate:modelValue":l[0]||(l[0]=s=>a(e).name=s),required:"",autofocus:"",autocomplete:"name",placeholder:o.lang().placeholder.name,error:a(e).errors.name},null,8,["modelValue","placeholder","error"]),r(n,{class:"mt-2",message:a(e).errors.name},null,8,["message"])]),t("div",k,[r(m,{for:"email",value:o.lang().label.email},null,8,["value"]),r(i,{id:"email",type:"email",class:"mt-1 block w-full",modelValue:a(e).email,"onUpdate:modelValue":l[1]||(l[1]=s=>a(e).email=s),required:"",autocomplete:"username",placeholder:o.lang().placeholder.email,error:a(e).errors.email},null,8,["modelValue","placeholder","error"]),r(n,{class:"mt-2",message:a(e).errors.email},null,8,["message"])]),t("div",$,[r(m,{for:"password",value:o.lang().label.password},null,8,["value"]),r(i,{id:"password",type:"password",class:"mt-1 block w-full",modelValue:a(e).password,"onUpdate:modelValue":l[2]||(l[2]=s=>a(e).password=s),required:"",autocomplete:"new-password",placeholder:o.lang().placeholder.password,error:a(e).errors.password},null,8,["modelValue","placeholder","error"]),r(n,{class:"mt-2",message:a(e).errors.password},null,8,["message"])]),t("div",q,[r(m,{for:"password_confirmation",value:o.lang().label.password_confirmation},null,8,["value"]),r(i,{id:"password_confirmation",type:"password",class:"mt-1 block w-full",modelValue:a(e).password_confirmation,"onUpdate:modelValue":l[3]||(l[3]=s=>a(e).password_confirmation=s),required:"",autocomplete:"new-password",placeholder:o.lang().placeholder.password_confirmation,error:a(e).errors.password_confirmation},null,8,["modelValue","placeholder","error"]),r(n,{class:"mt-2",message:a(e).errors.password_confirmation},null,8,["message"])]),t("div",U,[r(a(h),{href:o.route("login"),class:"underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary dark:focus:ring-offset-gray-800"},{default:d(()=>[g(_(o.lang().label.registered),1)]),_:1},8,["href"])])],40,y)]),_:1}))}};export{R as default};