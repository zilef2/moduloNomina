import{q as w,l as x,M as g,h,o as u,c as v,a as l,w as n,y as r,z as c,b as s,E as i,n as b,r as y,g as k,N as _,f as B}from"./app-6f12aaad.js";const E={class:"fixed inset-0 overflow-y-auto px-4 py-6 sm:px-0 z-50","scroll-region":""},C=s("div",{class:"absolute inset-0 bg-gray-500 dark:bg-gray-900 opacity-75"},null,-1),S=[C],M={__name:"Modal",props:{show:{type:Boolean,default:!1},maxWidth:{type:String,default:"2xl"},closeable:{type:Boolean,default:!1}},emits:["close"],setup(e,{emit:t}){const a=e,f=t;w(()=>a.show,()=>{a.show?document.body.style.overflow="hidden":document.body.style.overflow=null});const d=()=>{a.closeable&&f("close")},m=o=>{o.key==="Escape"&&a.show&&d()};x(()=>document.addEventListener("keydown",m)),g(()=>{document.removeEventListener("keydown",m),document.body.style.overflow=null});const p=h(()=>({sm:"sm:max-w-sm",md:"sm:max-w-md",lg:"sm:max-w-lg",xl:"sm:max-w-xl","2xl":"sm:max-w-2xl","3xl":"sm:max-w-3xl","4xl":"sm:max-w-4xl"})[a.maxWidth]);return(o,$)=>(u(),v(_,{to:"body"},[l(i,{"leave-active-class":"duration-200"},{default:n(()=>[r(s("div",E,[l(i,{"enter-active-class":"ease-out duration-200","enter-from-class":"opacity-0","enter-to-class":"opacity-100","leave-active-class":"ease-in duration-200","leave-from-class":"opacity-100","leave-to-class":"opacity-0"},{default:n(()=>[r(s("div",{class:"fixed inset-0 transform transition-all",onClick:d},S,512),[[c,e.show]])]),_:1}),l(i,{"enter-active-class":"ease-out duration-200","enter-from-class":"opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95","enter-to-class":"opacity-100 translate-y-0 sm:scale-100","leave-active-class":"ease-in duration-200","leave-from-class":"opacity-100 translate-y-0 sm:scale-100","leave-to-class":"opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"},{default:n(()=>[r(s("div",{class:b(["mb-6 bg-white dark:bg-gray-800 rounded-lg overflow-hidden shadow-xl transform transition-all sm:w-full sm:mx-auto",p.value])},[e.show?y(o.$slots,"default",{key:0}):k("",!0)],2),[[c,e.show]])]),_:3})],512),[[c,e.show]])]),_:3})]))}},N=["type"],V={__name:"SecondaryButton",props:{type:{type:String,default:"button"}},setup(e){return(t,a)=>(u(),B("button",{type:e.type,class:"inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 dark:focus:ring-offset-gray-800 disabled:opacity-25 transition ease-in-out duration-150"},[y(t.$slots,"default")],8,N))}};export{M as _,V as a};