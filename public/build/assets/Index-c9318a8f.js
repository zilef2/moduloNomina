import{l as j,J as V,m as B,O as P,f as g,a as r,u as d,w as c,F as k,p as D,o as i,X as U,b as t,q as m,s as h,d as N,t as n,c as b,x as A,y as T,A as E}from"./app-46106272.js";import{_ as F}from"./AuthenticatedLayout-e2b78f72.js";import{_ as q}from"./Breadcrumb-af75185f.js";import{a as G}from"./TextInput-3fa3f2f5.js";import{_ as J}from"./PrimaryButton-1e750976.js";import{_ as L}from"./SelectInput-26885325.js";import{_ as v}from"./DangerButton-39adbda9.js";import{_ as M}from"./Pagination-153a3b78.js";import{T as x,C as _,P as X}from"./index-eda91085.js";import z from"./Create-e8fdc2ea.js";import H from"./Edit-1149161d.js";import K from"./Delete-7b56ad58.js";import Q from"./DeleteBulk-5c8df03b.js";import{_ as R}from"./Checkbox-6e316493.js";import{_ as W}from"./InfoButton-dd5798aa.js";import"./InputError-fbff4dad.js";import"./SecondaryButton-7d303cdf.js";const Y={class:"space-y-4"},Z={class:"px-4 sm:px-0"},ee={class:"rounded-lg overflow-hidden w-fit"},te={class:"relative bg-white dark:bg-gray-800 shadow sm:rounded-lg"},se={class:"flex justify-between p-2"},le={class:"flex space-x-2"},ae={class:"overflow-x-auto scrollbar-table"},re={class:"w-full"},oe={class:"uppercase text-sm border-t border-gray-200 dark:border-gray-700"},ne={class:"dark:bg-gray-900/50 text-left"},de={class:"px-2 py-4 text-center"},ie=t("th",{class:"px-2 py-4 text-center"},"#",-1),pe={class:"flex justify-between items-center"},ce={class:"flex justify-between items-center"},me=t("span",null,"Guard",-1),ue={class:"flex justify-between items-center"},fe={class:"flex justify-between items-center"},he=t("th",{class:"px-2 py-4 sr-only"},"Action",-1),_e={class:"whitespace-nowrap py-4 px-2 sm:py-3 text-center"},ye=["value"],ge={class:"whitespace-nowrap py-4 px-2 sm:py-3 text-center"},be={class:"whitespace-nowrap py-4 px-2 sm:py-3"},we={class:"whitespace-nowrap py-4 px-2 sm:py-3"},ke={class:"whitespace-nowrap py-4 px-2 sm:py-3"},ve={class:"whitespace-nowrap py-4 px-2 sm:py-3"},xe={class:"whitespace-nowrap py-4 px-2 sm:py-3"},$e={class:"flex justify-center items-center"},Oe={class:"rounded-md overflow-hidden"},Ce={class:"flex justify-between items-center p-2 border-t border-gray-200 dark:border-gray-700"},Me={__name:"Index",props:{title:String,filters:Object,permissions:Object,breadcrumbs:Object,perPage:Number},setup(u){const o=u,{_:$,debounce:O,pickBy:C}=D,e=j({params:{search:o.filters.search,field:o.filters.field,order:o.filters.order,perPage:o.perPage},selectedId:[],multipleSelect:!1,createOpen:!1,editOpen:!1,deleteOpen:!1,deleteBulkOpen:!1,permission:null,dataSet:V().props.app.perpage}),f=a=>{e.params.field=a,e.params.order=e.params.order==="asc"?"desc":"asc"};B(()=>$.cloneDeep(e.params),O(()=>{let a=C(e.params);P.get(route("permission.index"),a,{replace:!0,preserveState:!0,preserveScroll:!0})},150));const I=a=>{var s;a.target.checked===!1?e.selectedId=[]:(s=o.permissions)==null||s.data.forEach(p=>{e.selectedId.push(p.id)})},S=()=>{var a;((a=o.permissions)==null?void 0:a.data.length)==e.selectedId.length?e.multipleSelect=!0:e.multipleSelect=!1};return(a,s)=>{const p=T("tooltip");return i(),g(k,null,[r(d(U),{title:o.title},null,8,["title"]),r(F,null,{default:c(()=>[r(q,{title:u.title,breadcrumbs:u.breadcrumbs},null,8,["title","breadcrumbs"]),t("div",Y,[t("div",Z,[t("div",ee,[m(r(J,{class:"rounded-none",onClick:s[0]||(s[0]=l=>e.createOpen=!0)},{default:c(()=>[N(n(a.lang().button.add),1)]),_:1},512),[[h,a.can(["create permission"])]]),r(z,{show:e.createOpen,onClose:s[1]||(s[1]=l=>e.createOpen=!1),title:o.title},null,8,["show","title"]),r(H,{show:e.editOpen,onClose:s[2]||(s[2]=l=>e.editOpen=!1),permission:e.permission,title:o.title},null,8,["show","permission","title"]),r(K,{show:e.deleteOpen,onClose:s[3]||(s[3]=l=>e.deleteOpen=!1),permission:e.permission,title:o.title},null,8,["show","permission","title"]),r(Q,{show:e.deleteBulkOpen,onClose:s[4]||(s[4]=l=>(e.deleteBulkOpen=!1,e.multipleSelect=!1,e.selectedId=[])),selectedId:e.selectedId,title:o.title},null,8,["show","selectedId","title"])])]),t("div",te,[t("div",se,[t("div",le,[r(L,{modelValue:e.params.perPage,"onUpdate:modelValue":s[5]||(s[5]=l=>e.params.perPage=l),dataSet:e.dataSet},null,8,["modelValue","dataSet"]),m((i(),b(v,{onClick:s[6]||(s[6]=l=>e.deleteBulkOpen=!0),class:"px-3 py-1.5"},{default:c(()=>[r(d(x),{class:"w-5 h-5"})]),_:1})),[[h,e.selectedId.length!=0&&a.can(["delete permission"])],[p,a.lang().tooltip.delete_selected]])]),r(G,{modelValue:e.params.search,"onUpdate:modelValue":s[7]||(s[7]=l=>e.params.search=l),type:"text",class:"block w-3/6 md:w-2/6 lg:w-1/6 rounded-lg",placeholder:a.lang().placeholder.search},null,8,["modelValue","placeholder"])]),t("div",ae,[t("table",re,[t("thead",oe,[t("tr",ne,[t("th",de,[r(R,{checked:e.multipleSelect,"onUpdate:checked":s[8]||(s[8]=l=>e.multipleSelect=l),onChange:I},null,8,["checked"])]),ie,t("th",{class:"px-2 py-4 cursor-pointer",onClick:s[9]||(s[9]=l=>f("name"))},[t("div",pe,[t("span",null,n(a.lang().label.name),1),r(d(_),{class:"w-4 h-4"})])]),t("th",{class:"px-2 py-4 cursor-pointer",onClick:s[10]||(s[10]=l=>f("guard"))},[t("div",ce,[me,r(d(_),{class:"w-4 h-4"})])]),t("th",{class:"px-2 py-4 cursor-pointer",onClick:s[11]||(s[11]=l=>f("created_at"))},[t("div",ue,[t("span",null,n(a.lang().label.created),1),r(d(_),{class:"w-4 h-4"})])]),t("th",{class:"px-2 py-4 cursor-pointer",onClick:s[12]||(s[12]=l=>f("updated_at"))},[t("div",fe,[t("span",null,n(a.lang().label.updated),1),r(d(_),{class:"w-4 h-4"})])]),he])]),t("tbody",null,[(i(!0),g(k,null,A(u.permissions.data,(l,w)=>(i(),g("tr",{key:w,class:"border-t border-gray-200 dark:border-gray-700 hover:bg-gray-200/30 hover:dark:bg-gray-900/20"},[t("td",_e,[m(t("input",{class:"rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-primary dark:text-primary shadow-sm focus:ring-primary/80 dark:focus:ring-primary dark:focus:ring-offset-gray-800 dark:checked:bg-primary dark:checked:border-primary",type:"checkbox",onChange:S,value:l.id,"onUpdate:modelValue":s[13]||(s[13]=y=>e.selectedId=y)},null,40,ye),[[E,e.selectedId]])]),t("td",ge,n(++w),1),t("td",be,n(l.name),1),t("td",we,n(l.guard_name),1),t("td",ke,n(l.created_at),1),t("td",ve,n(l.updated_at),1),t("td",xe,[t("div",$e,[t("div",Oe,[m((i(),b(W,{type:"button",onClick:y=>(e.editOpen=!0,e.permission=l),class:"px-2 py-1.5 rounded-none"},{default:c(()=>[r(d(X),{class:"w-4 h-4"})]),_:2},1032,["onClick"])),[[h,a.can(["update permission"])],[p,a.lang().tooltip.edit]]),m((i(),b(v,{type:"button",onClick:y=>(e.deleteOpen=!0,e.permission=l),class:"px-2 py-1.5 rounded-none"},{default:c(()=>[r(d(x),{class:"w-4 h-4"})]),_:2},1032,["onClick"])),[[h,a.can(["delete permission"])],[p,a.lang().tooltip.delete]])])])])]))),128))])])]),t("div",Ce,[r(M,{links:o.permissions,filters:e.params},null,8,["links","filters"])])])])]),_:1})],64)}}};export{Me as default};