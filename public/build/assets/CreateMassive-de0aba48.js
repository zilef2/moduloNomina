import{B as D,l as z,v as C,z as N,m as v,f as m,a as o,w as b,o as c,b as n,t as u,u as e,g,au as I,d as V,n as M,D as R}from"./app-74faa0bb.js";import{_ as $,a as H}from"./SecondaryButton-a1fedac6.js";import{_ as f}from"./InputError-815f255c.js";import{_ as d}from"./InputLabel-7cdc2767.js";import{_ as B}from"./PrimaryButton-a92129c8.js";import{_ as i}from"./TextInput-c1c823a9.js";import{_ as F}from"./SelectInput-40303077.js";import{Z as T}from"./main-0c2938db.js";import{a as K}from"./app-57141eb6.js";const O={class:"space-y-6"},Y={class:"p-6 mb-20"},E={class:"flex space-x-4"},P={class:"text-lg font-medium text-gray-900 dark:text-gray-100"},Z={class:"text-lg font-medium text-gray-900 dark:text-gray-100"},q={class:"text-lg font-medium text-gray-900 dark:text-gray-100"},J={class:"my-6 grid grid-cols-1 md:grid-cols-2 gap-6"},L={class:"mt-4 grid grid-cols-2 gap-6"},W={key:0,class:"mt-4 grid grid-cols-2 gap-6"},X={key:1,class:"grid grid-cols-2 gap-6"},A={key:2,class:"grid grid-cols-2 gap-6"},G={class:"mt-4"},Q={key:3,class:"mt-4"},aa=n("label",{class:"dark:text-white"},"Horario",-1),ea={class:"flex justify-end"},la={key:0,class:"m-2"},ca={__name:"CreateMassive",props:{show:Boolean,title:String,valoresSelect:Object,IntegerDefectoSelect:Number,horasemana:Number,startDateMostrar:String,endDateMostrar:String,numberPermissions:Number,ultimoReporte:Number},emits:["close"],setup(x,{emit:p}){const t=x;let k=new Date().getFullYear();K.getHolidaysByYear(k);let h=D(null);const w=22;9-t.ultimoReporte;let j=0;z({respuestaSeguro:"",startTime:{hours:7,minutes:0}});const a=C({fecha_ini:"",fecha_fin:"",centro_costo_id:t.IntegerDefectoSelect,observaciones:"",horas_trabajadas:"",almuerzo:"1",diurnas:0,nocturnas:0,extra_diurnas:0,extra_nocturnas:0,dominicales:"no",extra:"no",esFestivo:!1,dominical_diurnas:0,dominical_nocturnas:0,dominical_extra_diurnas:0,dominical_extra_nocturnas:0});t.numberPermissions>8,N(()=>{localStorage.getItem("centroCostoId")&&(a.centro_costo_id=localStorage.getItem("centroCostoId"))});let _="";const S=()=>{let r=Date.parse(a.fecha_fin),l=new Date(r);const s=l.getHours(),U=l.getMinutes();s==23&&U==59&&(a.horas_trabajadas++,a.nocturnas++)};v(()=>a.centro_costo_id,r=>{localStorage.setItem("centroCostoId",r)}),v(()=>a.fecha_ini,r=>{a.horas_trabajadas=8});const y=()=>{a.fecha_fin,S(),a.horas_trabajadas<=w&&a.horas_trabajadas!==0?Object.keys(a.errors).length===0?(a.almuerzo=j,a.post(route("MassiveReportes"),{preserveScroll:!0,onSuccess:()=>{},onError:()=>{alert(JSON.stringify(a.errors,null,4))},onFinish:()=>{}})):alert("Verifique de nuevo"):alert("Horas invalidas")};return(r,l)=>(c(),m("section",O,[o($,{show:t.show,onClose:l[14]||(l[14]=s=>p("close")),maxWidth:"4xl"},{default:b(()=>[n("form",Y,[n("div",E,[n("h2",P,[n("b",null,u(t.title),1)]),n("h2",Z," Horas semana: "+u(t==null?void 0:t.horasemana),1),n("h2",q,u(t==null?void 0:t.startDateMostrar)+" - "+u(t==null?void 0:t.endDateMostrar),1)]),n("div",J,[n("div",null,[o(d,{for:"fecha_ini",value:r.lang().label.fecha_ini},null,8,["value"]),o(e(T),{range:"",id:"fecha_ini",type:"date",class:"mt-1 block w-full",modelValue:e(a).fecha_ini,"onUpdate:modelValue":l[0]||(l[0]=s=>e(a).fecha_ini=s),placeholder:r.lang().placeholder.fecha_ini,error:e(a).errors.fecha_ini},null,8,["modelValue","placeholder","error"]),o(f,{class:"mt-2",message:e(a).errors.fecha_ini},null,8,["message"])]),n("div",null,[o(d,{for:"horas_trabajadas",value:r.lang().label.horas_trabajadas},null,8,["value"]),o(i,{id:"horas_trabajadas",type:"number",class:"bg-gray-100 dark:bg-gray-700 mt-1 block w-full",modelValue:e(a).horas_trabajadas,"onUpdate:modelValue":l[1]||(l[1]=s=>e(a).horas_trabajadas=s),disabled:"",placeholder:r.lang().placeholder.horas_trabajadas,error:e(a).errors.horas_trabajadas},null,8,["modelValue","placeholder","error"]),o(f,{class:"bg-gray-100 dark:bg-gray-700 mt-2",message:e(a).errors.horas_trabajadas},null,8,["message"])]),n("div",null,[o(d,{for:"almuerzo",value:r.lang().label.horacomida+" (+9 horas)"},null,8,["value"]),o(i,{id:"almuerzo",type:"text",class:"bg-gray-100 dark:bg-gray-700 mt-1 w-full",modelValue:e(a).almuerzo,"onUpdate:modelValue":l[2]||(l[2]=s=>e(a).almuerzo=s),disabled:"",placeholder:r.lang().placeholder.almuerzo,error:e(a).errors.almuerzo},null,8,["modelValue","placeholder","error"])]),n("div",L,[n("div",null,[o(d,{ref_key:"label_diurnas",ref:h,for:"diurnas",value:r.lang().label.diurnas},null,8,["value"]),o(i,{id:"diurnas",type:"number",class:"bg-gray-100 dark:bg-gray-700 mt-1 w-full",modelValue:e(a).diurnas,"onUpdate:modelValue":l[3]||(l[3]=s=>e(a).diurnas=s),disabled:"",placeholder:r.lang().placeholder.diurnas,error:e(a).errors.diurnas},null,8,["modelValue","placeholder","error"])]),n("div",null,[o(d,{ref:"label_nocturnas",for:"nocturnas",value:r.lang().label.nocturnas},null,8,["value"]),o(i,{id:"nocturnas",type:"number",class:"bg-gray-100 dark:bg-gray-700 mt-1 w-full",modelValue:e(a).nocturnas,"onUpdate:modelValue":l[4]||(l[4]=s=>e(a).nocturnas=s),disabled:"",placeholder:r.lang().placeholder.nocturnas,error:e(a).errors.nocturnas},null,8,["modelValue","placeholder","error"])])]),e(a).extra_diurnas||e(a).extra_nocturnas||e(a).dominicales=="si"?(c(),m("div",W,[n("div",null,[o(d,{ref:"label_extra_diurnas",for:"extra_diurnas",value:r.lang().label.extra_diurnas},null,8,["value"]),o(i,{id:"extra_diurnas",type:"number",class:"bg-gray-100 dark:bg-gray-700 mt-1 w-full",modelValue:e(a).extra_diurnas,"onUpdate:modelValue":l[5]||(l[5]=s=>e(a).extra_diurnas=s),disabled:"",placeholder:r.lang().placeholder.extra_diurnas,error:e(a).errors.extra_diurnas},null,8,["modelValue","placeholder","error"])]),n("div",null,[o(d,{ref:"label_extra_nocturnas",for:"extra_nocturnas",value:r.lang().label.extra_nocturnas},null,8,["value"]),o(i,{id:"extra_nocturnas",type:"number",class:"bg-gray-100 dark:bg-gray-700 mt-1 w-full",modelValue:e(a).extra_nocturnas,"onUpdate:modelValue":l[6]||(l[6]=s=>e(a).extra_nocturnas=s),disabled:"",placeholder:r.lang().placeholder.extra_nocturnas,error:e(a).errors.extra_nocturnas},null,8,["modelValue","placeholder","error"])])])):g("",!0),e(a).dominicales=="si"?(c(),m("div",X,[n("div",null,[o(d,{ref_key:"label_diurnas",ref:h,for:"dominical_diurnas",value:r.lang().label.dominical_diurnas},null,8,["value"]),o(i,{id:"dominical_diurnas",type:"number",class:"bg-gray-100 dark:bg-gray-700 mt-1 w-full",modelValue:e(a).dominical_diurnas,"onUpdate:modelValue":l[7]||(l[7]=s=>e(a).dominical_diurnas=s),disabled:"",placeholder:r.lang().placeholder.dominical_diurnas,error:e(a).errors.dominical_diurnas},null,8,["modelValue","placeholder","error"])]),n("div",null,[o(d,{ref:"label_nocturnas",for:"dominical_nocturnas",value:r.lang().label.dominical_nocturnas},null,8,["value"]),o(i,{id:"dominical_nocturnas",type:"number",class:"bg-gray-100 dark:bg-gray-700 mt-1 w-full",modelValue:e(a).dominical_nocturnas,"onUpdate:modelValue":l[8]||(l[8]=s=>e(a).dominical_nocturnas=s),disabled:"",placeholder:r.lang().placeholder.dominical_nocturnas,error:e(a).errors.dominical_nocturnas},null,8,["modelValue","placeholder","error"])])])):g("",!0),e(a).dominicales=="si"?(c(),m("div",A,[n("div",null,[o(d,{ref:"label_extra_diurnas",for:"dominical_extra_diurnas",value:r.lang().label.dominical_extra_diurnas},null,8,["value"]),o(i,{id:"dominical_extra_diurnas",type:"number",class:"bg-gray-100 dark:bg-gray-700 mt-1 w-full",modelValue:e(a).dominical_extra_diurnas,"onUpdate:modelValue":l[9]||(l[9]=s=>e(a).dominical_extra_diurnas=s),disabled:"",placeholder:r.lang().placeholder.dominical_extra_diurnas,error:e(a).errors.dominical_extra_diurnas},null,8,["modelValue","placeholder","error"])]),n("div",null,[o(d,{ref:"label_extra_nocturnas",for:"dominical_extra_nocturnas",value:r.lang().label.dominical_extra_nocturnas},null,8,["value"]),o(i,{id:"dominical_extra_nocturnas",type:"number",class:"bg-gray-100 dark:bg-gray-700 mt-1 w-full",modelValue:e(a).dominical_extra_nocturnas,"onUpdate:modelValue":l[10]||(l[10]=s=>e(a).dominical_extra_nocturnas=s),disabled:"",placeholder:r.lang().placeholder.dominical_extra_nocturnas,error:e(a).errors.dominical_extra_nocturnas},null,8,["modelValue","placeholder","error"])])])):g("",!0),n("div",G,[o(d,{for:"centro_costo_id",value:r.lang().label.centro_costo_id},null,8,["value"]),o(F,{modelValue:e(a).centro_costo_id,"onUpdate:modelValue":l[11]||(l[11]=s=>e(a).centro_costo_id=s),dataSet:t.valoresSelect,class:"mt-1 block w-full"},null,8,["modelValue","dataSet"]),o(f,{class:"mt-2",message:e(a).errors.centro_costo_id},null,8,["message"])]),e(a).dominicales=="si"?(c(),m("div",Q,[aa,o(i,{id:"dominicales",type:"text",class:"bg-gray-100 dark:bg-gray-700 block w-full",modelValue:e(_),"onUpdate:modelValue":l[12]||(l[12]=s=>I(_)?_.value=s:_=s),disabled:""},null,8,["modelValue"])])):g("",!0)]),n("div",ea,[t.ultimoReporte>0?(c(),m("p",la,"Hay pendientes "+u(t.ultimoReporte)+" horas",1)):g("",!0),o(H,{disabled:e(a).processing,onClick:l[13]||(l[13]=s=>p("close"))},{default:b(()=>[V(u(r.lang().button.close),1)]),_:1},8,["disabled"]),o(B,{class:M(["ml-3",{"opacity-25":e(a).processing}]),disabled:e(a).processing,onClick:y,onKeyup:R(y,["enter"])},{default:b(()=>[V(u(e(a).processing?r.lang().button.add+"...":r.lang().button.add),1)]),_:1},8,["class","disabled","onKeyup"])])])]),_:1},8,["show"])]))}};export{ca as default};