import{J as k,v as b,f as m,b as s,t as r,a as l,u as e,d,w as u,q as x,s as V,g as p,n as w,T as S,e as B,o as f,h as N}from"./app-1163a5a6.js";import{_ as g}from"./InputError-a6fa3c9e.js";import{_ as v,a as y}from"./TextInput-e3d3e8ef.js";import{_ as $}from"./PrimaryButton-4edf888a.js";const C={class:"text-lg font-medium text-gray-900 dark:text-gray-100"},E={class:"mt-1 text-sm text-gray-600 dark:text-gray-400"},T=["onSubmit"],U={key:0},q={class:"text-sm mt-2 text-gray-800 dark:text-gray-200"},D={class:"flex items-center gap-4"},P={key:0,class:"text-sm text-gray-600 dark:text-gray-400"},M={__name:"UpdateProfileInformationForm",props:{mustVerifyEmail:Boolean,status:String},setup(_){const c=_,i=k().props.auth.user,a=b({name:i.name,email:i.email}),h=()=>{a.patch(route("profile.update"),{preserveScroll:!0})};return(t,o)=>(f(),m("section",null,[s("header",null,[s("h2",C,r(t.lang().profile.profile_information),1),s("p",E,r(t.lang().profile.update_profile),1)]),s("form",{onSubmit:B(h,["prevent"]),class:"mt-6 space-y-6"},[s("div",null,[l(v,{for:"name",value:t.lang().label.name},null,8,["value"]),l(y,{id:"name",type:"text",class:"mt-1 block w-full",modelValue:e(a).name,"onUpdate:modelValue":o[0]||(o[0]=n=>e(a).name=n),required:"",autofocus:"",autocomplete:"name",placeholder:t.lang().placeholder.name,error:e(a).errors.name},null,8,["modelValue","placeholder","error"]),l(g,{class:"mt-2",message:e(a).errors.name},null,8,["message"])]),s("div",null,[l(v,{for:"email",value:t.lang().label.email},null,8,["value"]),l(y,{id:"email",type:"email",modelValue:e(a).email,"onUpdate:modelValue":o[1]||(o[1]=n=>e(a).email=n),disabled:"",autocomplete:"email",placeholder:t.lang().placeholder.email,error:e(a).errors.email,class:"mt-1 block w-full bg-slate-400 dark:bg-slate-600"},null,8,["modelValue","placeholder","error"]),l(g,{class:"mt-2",message:e(a).errors.email},null,8,["message"])]),c.mustVerifyEmail&&e(i).email_verified_at===null?(f(),m("div",U,[s("p",q,[d(r(t.lang().profile.unverified_email)+" ",1),l(e(N),{href:t.route("verification.send"),method:"post",as:"button",class:"underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary dark:focus:ring-offset-gray-800"},{default:u(()=>[d(r(t.lang().profile.resend_email_verification),1)]),_:1},8,["href"])]),x(s("div",{class:"mt-2 font-medium text-sm text-green-600 dark:text-green-400"},r(t.lang().profile.sent_verification_email),513),[[V,c.status==="verification-link-sent"]])])):p("",!0),s("div",D,[l($,{class:w({"opacity-25":e(a).processing}),disabled:e(a).processing},{default:u(()=>[d(r(e(a).processing?t.lang().button.save+"...":t.lang().button.save),1)]),_:1},8,["class","disabled"]),l(S,{"enter-from-class":"opacity-0","leave-to-class":"opacity-0",class:"transition ease-in-out"},{default:u(()=>[e(a).recentlySuccessful?(f(),m("p",P,r(t.lang().profile.saved),1)):p("",!0)]),_:1})])],40,T)]))}};export{M as default};