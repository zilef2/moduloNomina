import{v as _,f as g,a as d,w as u,o as b,b as t,t as o,d as n,u as l,n as h,e as y}from"./app-74faa0bb.js";import{_ as w}from"./DangerButton-09f30be1.js";import{_ as x,a as k}from"./SecondaryButton-a1fedac6.js";const v={class:"space-y-6"},R=["onSubmit"],S={class:"text-lg font-medium text-gray-900 dark:text-gray-100"},C={class:"mt-1 text-sm text-gray-600 dark:text-gray-400"},$={class:"mt-6 flex justify-end"},E={__name:"Recontratar",props:{show:Boolean,title:String,user:Object},emits:["close"],setup(f,{emit:i}){var p;const a=f,e=_({userid:(p=a.user)==null?void 0:p.id}),m=()=>{var s;e.post(route("Recontratar",(s=a.user)==null?void 0:s.id),{preserveScroll:!0,onSuccess:()=>{i("close"),e.reset()},onError:()=>null,onFinish:()=>null})};return(s,r)=>(b(),g("section",v,[d(x,{show:a.show,onClose:r[1]||(r[1]=c=>i("close")),maxWidth:"lg"},{default:u(()=>{var c;return[t("form",{class:"p-6",onSubmit:y(m,["prevent"])},[t("h2",S,o(s.lang().label.Recontratar),1),t("p",C,[n(o(s.lang().label.Recontratar_confirm)+" ",1),t("b",null,o((c=a.user)==null?void 0:c.name),1),n("? ")]),t("div",$,[d(k,{disabled:l(e).processing,onClick:r[0]||(r[0]=B=>i("close"))},{default:u(()=>[n(o(s.lang().button.close),1)]),_:1},8,["disabled"]),d(w,{class:h(["ml-3",{"opacity-25":l(e).processing}]),disabled:l(e).processing,onClick:m},{default:u(()=>[n(o(l(e).processing?s.lang().button.Recontratar+"...":s.lang().button.Recontratar),1)]),_:1},8,["class","disabled"])])],40,R)]}),_:1},8,["show"])]))}};export{E as default};