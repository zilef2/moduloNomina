import{l as _,v as b,k as g,f as h,a as r,w as c,o as v,b as l,t,u as s,d as u,n as w,e as y}from"./app-74faa0bb.js";import{_ as C}from"./InputError-815f255c.js";import{_ as S}from"./InputLabel-7cdc2767.js";import{_ as $,a as k}from"./SecondaryButton-a1fedac6.js";import{_ as V}from"./PrimaryButton-a92129c8.js";import{_ as B}from"./TextInput-c1c823a9.js";const x={class:"space-y-6"},E=["onSubmit"],N={class:"text-lg font-medium text-gray-900 dark:text-gray-100"},j={class:"my-6 grid grid-cols-1 gap-6"},q={class:"flex justify-end"},A={__name:"Create",props:{show:Boolean,title:String},emits:["close"],setup(f,{emit:n}){const i=f,p=_({multipleSelect:!1}),e=b({nombre:""}),d=()=>{e.post(route("CentroCostos.store"),{preserveScroll:!0,onSuccess:()=>{n("close"),e.reset(),p.multipleSelect=!1},onError:()=>alert(errors.create),onFinish:()=>null})};return g(()=>{i.show&&(e.errors={})}),(o,a)=>(v(),h("section",x,[r($,{show:i.show,onClose:a[2]||(a[2]=m=>n("close"))},{default:c(()=>[l("form",{class:"p-6",onSubmit:y(d,["prevent"])},[l("h2",N,t(o.lang().label.add)+" "+t(i.title),1),l("div",j,[l("div",null,[r(S,{for:"nombre",value:o.lang().label.name},null,8,["value"]),r(B,{id:"nombre",type:"text",class:"mt-1 block w-full",modelValue:s(e).nombre,"onUpdate:modelValue":a[0]||(a[0]=m=>s(e).nombre=m),required:"",placeholder:o.lang().placeholder.nombre,error:s(e).errors.nombre},null,8,["modelValue","placeholder","error"]),r(C,{class:"mt-2",message:s(e).errors.nombre},null,8,["message"])])]),l("div",q,[r(k,{disabled:s(e).processing,onClick:a[1]||(a[1]=m=>n("close"))},{default:c(()=>[u(t(o.lang().button.close),1)]),_:1},8,["disabled"]),r(V,{class:w(["ml-3",{"opacity-25":s(e).processing}]),disabled:s(e).processing,onClick:d},{default:c(()=>[u(t(s(e).processing?o.lang().button.add+"...":o.lang().button.add),1)]),_:1},8,["class","disabled"])])],40,E)]),_:1},8,["show"])]))}};export{A as default};