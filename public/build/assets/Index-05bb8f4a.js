import{o as a,f as i,r as z,l as H,J,m as G,O as K,v as Q,a as c,u,w as f,F as k,p as W,X as Y,b as e,c as y,d as Z,t as d,g as m,q as h,s as w,A as N,x as v,y as ee,e as te,n as se}from"./app-1163a5a6.js";import{_ as oe}from"./AuthenticatedLayout-0be2cef1.js";import{_ as re}from"./Breadcrumb-db0acd6b.js";import{a as ae}from"./TextInput-e3d3e8ef.js";import{_ as ne}from"./PrimaryButton-4edf888a.js";import{_ as le}from"./SelectInput-80e572af.js";import{_ as I}from"./DangerButton-954a992c.js";import{_ as ie}from"./Pagination-7d80b58e.js";import{T as R,C as de,P as ce,a as U,D as pe,X as ue}from"./index-9a2dc383.js";import{_ as me}from"./Checkbox-9680a516.js";import{_ as V}from"./InfoButton-90a373ae.js";import he from"./Create-c949865f.js";import j from"./Edit-15d97707.js";import ye from"./Delete-7021e57d.js";import{f as P,n as B}from"./global-4f731bd7.js";import{C as fe,p as be,a as _e,b as we,B as ge,c as ke,L as ve,d as xe}from"./index-7800a8f5.js";import"./SecondaryButton-1c05a758.js";import"./InputError-a6fa3c9e.js";import"./main-e5fab067.js";import"./app-57141eb6.js";const Ce=["type"],Se={__name:"SuccessButton",props:{type:{type:String,default:"submit"}},setup(p){return(t,x)=>(a(),i("button",{type:p.type,class:"inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-500 active:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150"},[z(t.$slots,"default")],8,Ce))}},Oe={class:"space-y-4"},De={class:"px-4 sm:px-0"},$e={class:"rounded-lg overflow-hidden w-fit"},Ne={class:"relative bg-white dark:bg-gray-800 shadow sm:rounded-lg"},Ie={class:"flex justify-between p-2"},Re={class:"flex space-x-2"},Ue={key:1,class:"flex space-x-8"},Ve=e("label",{for:"soloval"},"Solo validos",-1),je={class:"overflow-x-auto scrollbar-table"},Pe={class:"w-full"},Be={class:"uppercase text-sm border-t border-gray-200 dark:border-gray-700"},Te={class:"dark:bg-gray-900 text-left"},Me={class:"px-2 py-4 text-center"},Ee=["onClick"],Ae={class:"flex justify-between items-center"},qe={class:"whitespace-nowrap py-4 px-2 sm:py-3 text-center"},Le=["value"],Fe={class:"whitespace-nowrap py-4 px-2 sm:py-3"},Xe={class:"flex justify-start items-center"},ze={class:"flex rounded-md overflow-hidden"},He=["onSubmit"],Je={class:"whitespace-nowrap py-4 px-2 sm:py-3"},Ge={class:"whitespace-nowrap py-4 px-2 sm:py-3"},Ke={key:0},Qe={key:1},We={key:2},Ye={key:3},Ze={key:0},et={key:1},tt={key:2},st={key:4},ot={key:5},rt={class:"my-2 py-4 border-t border-gray-200 dark:border-gray-900 hover:bg-sky-200 hover:dark:bg-gray-900/20"},at=e("td",{class:"whitespace-nowrap py-4 px-2 sm:py-3"},null,-1),nt={class:"whitespace-nowrap py-4 px-2 sm:py-3"},lt=e("td",{class:"whitespace-nowrap py-4 px-2 sm:py-3"}," Totales ",-1),it=e("td",{class:"whitespace-nowrap py-4 px-2 sm:py-3"},null,-1),dt={key:0,class:"whitespace-nowrap py-4 px-2 sm:py-3"},ct=e("td",{class:"whitespace-nowrap py-4 px-2 sm:py-3"},null,-1),pt=e("td",{class:"whitespace-nowrap py-4 px-2 sm:py-3"},null,-1),ut=e("td",{class:"whitespace-nowrap py-4 px-2 sm:py-3"},null,-1),mt={class:"whitespace-nowrap py-4 px-2 sm:py-3"},ht={class:"whitespace-nowrap py-4 px-2 sm:py-3"},yt={class:"whitespace-nowrap py-4 px-2 sm:py-3"},ft={class:"whitespace-nowrap py-4 px-2 sm:py-3"},bt={class:"whitespace-nowrap py-4 px-2 sm:py-3"},_t={class:"whitespace-nowrap py-4 px-2 sm:py-3"},wt={class:"whitespace-nowrap py-4 px-2 sm:py-3"},gt={class:"whitespace-nowrap py-4 px-2 sm:py-3"},kt={class:"whitespace-nowrap py-4 px-2 sm:py-3"},vt=e("td",{class:"whitespace-nowrap py-4 px-2 sm:py-3"},null,-1),xt=e("td",{class:"whitespace-nowrap py-4 px-2 sm:py-3"},null,-1),Ct={class:"flex justify-betwween items-center p-2 border-t border-gray-200 dark:border-gray-700"},St={key:0,class:"text-gray-600 body-font overflow-hidden"},Ot={class:"container px-5 py-4 mx-auto"},Dt={class:"flex flex-wrap m-2"},$t={class:"p-1 md:w-1/2 flex flex-col items-start"},Nt=e("span",{class:"inline-block py-1 px-2 rounded bg-indigo-50 text-indigo-500 text-xs font-medium tracking-widest"},"Este mes",-1),It={class:"sm:text-3xl text-2xl title-font font-medium text-gray-900 mt-4 mb-4"},Rt={class:"m-1 p-1 w-full"},Yt={__name:"Index",props:{title:String,filters:Object,fromController:Object,breadcrumbs:Object,perPage:Number,nombresTabla:Array,valoresSelect:Object,showSelect:Object,showUsers:Object,IntegerDefectoSelect:Number,horasemana:Number,startDateMostrar:String,endDateMostrar:String,sumhoras_trabajadas:Number,quincena:Object,nombrePersona:String,numberPermissions:Number,ultimoReporte:Number,sumdiurnas:Number,sumnocturnas:Number,sumextra_diurnas:Number,sumextra_nocturnas:Number,sumdominical_diurno:Number,sumdominical_nocturno:Number,sumdominical_extra_diurno:Number,sumdominical_extra_nocturno:Number},setup(p){var S,O,D,$;const t=p;fe.register(be,_e,we,ge,ke,ve);const{_:x,debounce:T,pickBy:M}=W;typeof t.ultimoReporte<"u";const E={responsive:!0,maintainAspectRatio:!1,color:"#000",darkcolor:"#fff"},A={name:"Numero de reportes",datasets:[{label:"Numero de Reportes",data:t.quincena,backgroundColor:"#f87979"}]},s=H({params:{soloValidos:(S=t.filters)==null?void 0:S.soloValidos,search:(O=t.filters)==null?void 0:O.search,field:(D=t.filters)==null?void 0:D.field,order:($=t.filters)==null?void 0:$.order,perPage:t.perPage},selectedId:[],multipleSelect:!1,createOpen:!1,editOpen:!1,editCorregirOpen:!1,deleteOpen:!1,deleteBulkOpen:!1,generico:null,nope:null,dataSet:J().props.app.perpage}),q=o=>{o!=null&&o!=null&&(o=o.substr(2),s.params.field=o.replace(/ /g,"_"),s.params.order=s.params.order==="asc"?"desc":"asc")};G(()=>x.cloneDeep(s.params),T(()=>{let o=M(s.params);K.get(route("Reportes.index"),o,{replace:!0,preserveState:!0,preserveScroll:!0})},150));const L=o=>{var n;o.target.checked===!1?s.selectedId=[]:(n=t.fromController)==null||n.data.forEach(b=>{s.selectedId.push(b.id)})},F=()=>{var o;((o=t.fromController)==null?void 0:o.data.length)==s.selectedId.length?s.multipleSelect=!0:s.multipleSelect=!1},g=Q({valido:!1}),C=o=>{var n;g.valido=o,g.put(route("Reportes.update",(n=s.generico)==null?void 0:n.id),{preserveScroll:!0,onSuccess:()=>{g.reset()},onError:()=>null,onFinish:()=>null})};return(o,n)=>{const b=ee("tooltip");return a(),i(k,null,[c(u(Y),{title:t.title},null,8,["title"]),c(oe,null,{default:f(()=>[c(re,{title:p.title,breadcrumbs:p.breadcrumbs},null,8,["title","breadcrumbs"]),e("div",Oe,[e("div",De,[e("div",$e,[o.can(["create reporte"])?(a(),y(ne,{key:0,class:"rounded-none",onClick:n[0]||(n[0]=r=>s.createOpen=!0)},{default:f(()=>[Z(d(o.lang().button.add),1)]),_:1})):m("",!0),c(he,{show:s.createOpen,onClose:n[1]||(n[1]=r=>s.createOpen=!1),title:t.title,valoresSelect:t.valoresSelect,IntegerDefectoSelect:t.IntegerDefectoSelect,horasemana:t.horasemana,startDateMostrar:t.startDateMostrar,endDateMostrar:t.endDateMostrar,numberPermissions:t.numberPermissions,ultimoReporte:t.ultimoReporte},null,8,["show","title","valoresSelect","IntegerDefectoSelect","horasemana","startDateMostrar","endDateMostrar","numberPermissions","ultimoReporte"]),c(j,{show:s.editOpen,onClose:n[2]||(n[2]=r=>s.editOpen=!1),Reporte:s.generico,title:t.title,valoresSelect:t.valoresSelect,showUsers:t.showUsers,correccionUsuario:!1},null,8,["show","Reporte","title","valoresSelect","showUsers"]),c(j,{show:s.editCorregirOpen,onClose:n[3]||(n[3]=r=>s.editCorregirOpen=!1),Reporte:s.generico,title:t.title,valoresSelect:t.valoresSelect,showUsers:t.showUsers,correccionUsuario:!0},null,8,["show","Reporte","title","valoresSelect","showUsers"]),c(ye,{show:s.deleteOpen,onClose:n[4]||(n[4]=r=>s.deleteOpen=!1),Reporte:s.generico,title:t.title},null,8,["show","Reporte","title"])])]),e("div",Ne,[e("div",Ie,[e("div",Re,[p.filters!==null?(a(),y(le,{key:0,modelValue:s.params.perPage,"onUpdate:modelValue":n[5]||(n[5]=r=>s.params.perPage=r),dataSet:s.dataSet},null,8,["modelValue","dataSet"])):m("",!0),h((a(),y(I,{onClick:n[6]||(n[6]=r=>s.deleteBulkOpen=!0),class:"px-3 py-1.5"},{default:f(()=>[c(u(R),{class:"w-5 h-5"})]),_:1})),[[w,s.selectedId.length!=0],[b,o.lang().tooltip.delete_selected]])]),p.filters!==null?h((a(),y(ae,{key:0,modelValue:s.params.search,"onUpdate:modelValue":n[7]||(n[7]=r=>s.params.search=r),type:"text",class:"block w-3/6 md:w-2/6 lg:w-1/6 rounded-lg",placeholder:o.lang().placeholder.searchDates},null,8,["modelValue","placeholder"])),[[w,o.can(["update reporte"])]]):m("",!0),t.quincena===null?(a(),i("div",Ue,[Ve,p.filters!==null?h((a(),i("input",{key:0,"onUpdate:modelValue":n[8]||(n[8]=r=>s.params.soloValidos=r),id:"soloval",type:"checkbox",class:"bg-black h-7 w-7"},null,512)),[[N,s.params.soloValidos]]):m("",!0)])):m("",!0)]),e("div",je,[e("table",Pe,[e("thead",Be,[e("tr",Te,[e("th",Me,[c(me,{checked:s.multipleSelect,"onUpdate:checked":n[9]||(n[9]=r=>s.multipleSelect=r),onChange:L},null,8,["checked"])]),(a(!0),i(k,null,v(p.nombresTabla[0],(r,_)=>(a(),i("th",{key:_,onClick:l=>q(p.nombresTabla[2][_]),class:"px-2 py-4 cursor-pointer hover:bg-sky-50 dark:hover:bg-sky-800"},[e("div",Ae,[e("span",null,d(r),1),p.nombresTabla[2][_]!==null?(a(),y(u(de),{key:0,class:"w-4 h-4"})):m("",!0)])],8,Ee))),128))])]),e("tbody",null,[(a(!0),i(k,null,v(p.fromController.data,(r,_)=>(a(),i("tr",{key:_,class:"border-t border-gray-200 dark:border-gray-700 hover:bg-gray-200/30 hover:dark:bg-gray-900/20"},[e("td",qe,[h(e("input",{type:"checkbox",onChange:F,value:r.id,"onUpdate:modelValue":n[10]||(n[10]=l=>s.selectedId=l),class:"rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-primary dark:text-primary shadow-sm focus:ring-primary/80 dark:focus:ring-primary dark:focus:ring-offset-gray-800 dark:checked:bg-primary dark:checked:border-primary"},null,40,Le),[[N,s.selectedId]])]),e("td",Fe,[e("div",Xe,[e("div",ze,[e("form",{onSubmit:te(C,["prevent"])},[o.can(["update reporte"])?h((a(),y(V,{key:0,type:"button",onClick:l=>(s.editOpen=!0,s.generico=r),class:"px-2 py-1.5 rounded-none"},{default:f(()=>[c(u(ce),{class:"w-4 h-4"})]),_:2},1032,["onClick"])),[[b,o.lang().tooltip.edit]]):m("",!0),o.can(["update reporte"])?(a(),y(Se,{key:1,type:"button",class:se(["ml-3",{"opacity-25":u(g).processing}]),disabled:u(g).processing,onClick:l=>(s.generico=r,C(!0))},{default:f(()=>[c(u(U),{class:"w-4 h-4"})]),_:2},1032,["class","disabled","onClick"])):m("",!0)],40,He),o.can(["delete reporte"])&&r.valido===2?h((a(),y(V,{key:0,type:"button",onClick:l=>(s.editCorregirOpen=!0,s.generico=r),class:"px-2 py-1.5 rounded-none"},{default:f(()=>[c(u(pe),{class:"w-4 h-4"})]),_:2},1032,["onClick"])),[[b,o.lang().tooltip.edit]]):m("",!0),o.can(["delete reporte"])?h((a(),y(I,{key:1,type:"button",onClick:l=>(s.deleteOpen=!0,s.generico=r),class:"px-2 py-1.5 rounded-none"},{default:f(()=>[c(u(R),{class:"w-4 h-4"})]),_:2},1032,["onClick"])),[[b,o.lang().tooltip.delete]]):m("",!0)])])]),e("td",Je,d(_+1),1),e("td",Ge,d(p.showSelect[r.centro_costo_id]),1),h(e("td",{class:"whitespace-nowrap py-4 px-2 sm:py-3"},d(p.showUsers[r.user_id]),513),[[w,o.can(["updateCorregido reporte"])]]),(a(!0),i(k,null,v(p.nombresTabla[1],(l,X)=>(a(),i("td",{key:X,class:"whitespace-nowrap py-4 px-2 sm:py-3"},[l.substr(0,1)=="s"?(a(),i("div",Ke,d(r[l.substr(2)]),1)):l.substr(0,1)=="d"?(a(),i("div",Qe,d(u(P)(r[l.substr(2)])),1)):l.substr(0,1)=="t"?(a(),i("div",We,d(u(P)(r[l.substr(2)],"conLaHora")),1)):l.substr(0,1)=="b"?(a(),i("div",Ye,[r[l.substr(2)]===0?(a(),i("div",Ze," Aun no validada")):r[l.substr(2)]===1?(a(),i("div",et,[c(u(U),{class:"w-8 h-8 text-green-600"})])):r[l.substr(2)]===2?(a(),i("div",tt,[c(u(ue),{class:"w-8 h-8 text-red-600"})])):m("",!0)])):l.substr(0,1)=="i"?(a(),i("div",st,d(u(B)(r[l.substr(2)])),1)):l.substr(0,1)=="m"?(a(),i("div",ot,d(u(B)(r[l.substr(2)],0,1)),1)):m("",!0)]))),128))]))),128)),h(e("tr",rt,[at,h(e("td",nt,null,512),[[w,o.can(["updateCorregido reporte"])]]),lt,it,p.numberPermissions>1?(a(),i("td",dt)):m("",!0),ct,pt,ut,e("td",mt," Trabajadas: "+d(t.sumhoras_trabajadas),1),e("td",ht,d(t.sumdiurnas?"Diurnas: "+t.sumdiurnas:""),1),e("td",yt,d(t.sumnocturnas?"Nocturnas: "+t.sumnocturnas:""),1),e("td",ft,d(t.sumextra_diurnas?"Extra diurnas: "+t.sumextra_diurnas:""),1),e("td",bt,d(t.sumextra_nocturnas?"Extra nocturnas: "+t.sumextra_nocturnas:""),1),e("td",_t,d(t.sumdominical_diurno?"Dominical diurno: "+t.sumdominical_diurno:""),1),e("td",wt,d(t.sumdominical_nocturno?"Dominical nocturno: "+t.sumdominical_nocturno:""),1),e("td",gt,d(t.sumdominical_extra_diurno?"Dominical extra diurno: "+t.sumdominical_extra_diurno:""),1),e("td",kt,d(t.sumdominical_extra_nocturno?"Dominical extra nocturno: "+t.sumdominical_extra_nocturno:""),1),vt,xt],512),[[w,t.sumhoras_trabajadas!=0]])])])]),e("div",Ct,[c(ie,{links:t.fromController,filters:s.params},null,8,["links","filters"])])]),t.quincena!=null?h((a(),i("section",St,[e("div",Ot,[e("div",Dt,[e("div",$t,[Nt,e("h2",It,"Numero de reportes de "+d(t.nombrePersona),1),e("div",Rt,[c(u(xe),{id:"my-chart-id",options:E,data:A})])])])])],512)),[[w,o.can(["updateCorregido reporte"])||o.can(["isAdmin"])||o.can(["isadministrativo"])]]):m("",!0)])]),_:1})],64)}}};export{Yt as default};