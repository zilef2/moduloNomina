import{k as r,o as h,f as p,b as s,y as n,B as a,z as i}from"./app-6f12aaad.js";const m={class:"example-2"},v={class:"icon-content"},k={class:"container"},b=s("div",{class:"checkmark"},null,-1),x=s("div",{class:"tooltip"},"Solo validos",-1),g={class:"icon-content"},f={class:"container"},y=s("div",{class:"checkmark"},null,-1),B=s("div",{class:"tooltip"},"Esta quincena",-1),C={class:"icon-content"},V={class:"container"},N=s("div",{class:"checkmark"},null,-1),U=s("div",{class:"tooltip"},"(HD + HN) No son las H trabajadas",-1),H={class:"icon-content"},P={class:"container"},S=s("div",{class:"checkmark"},null,-1),w=s("div",{class:"tooltip"},"Siigo (solo extras)",-1),F={__name:"FilterButtons",props:{numberPermissions:Number},emits:["update:checked"],setup(d,{emit:u}){const c=d,_=u,e=r([!1,!1,!1]),l=()=>{_("update:checked",e.value)};return(D,o)=>(h(),p("ul",m,[s("li",v,[s("label",k,[n(s("input",{type:"checkbox","onUpdate:modelValue":o[0]||(o[0]=t=>e.value[0]=t),onChange:l},null,544),[[a,e.value[0]]]),b]),x]),s("li",g,[s("label",f,[n(s("input",{type:"checkbox","onUpdate:modelValue":o[1]||(o[1]=t=>e.value[1]=t),onChange:l},null,544),[[a,e.value[1]]]),y]),B]),n(s("li",C,[s("label",V,[n(s("input",{type:"checkbox","onUpdate:modelValue":o[2]||(o[2]=t=>e.value[2]=t),onChange:l,class:"bg-red-300"},null,544),[[a,e.value[2]]]),N]),U],512),[[i,c.numberPermissions>1]]),n(s("li",H,[s("label",P,[n(s("input",{type:"checkbox","onUpdate:modelValue":o[3]||(o[3]=t=>e.value[3]=t),onChange:l,class:"bg-red-300"},null,544),[[a,e.value[3]]]),S]),w],512),[[i,c.numberPermissions>1]])]))}};export{F as _};