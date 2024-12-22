//require: ajax.js
//v0.1.5
let usersign=(function(){
    let issignin= false,
        uid= undefined,
        gettime= 0;
    let usersign={};
    
    usersign.use=function(mustcheck,getinfo){
        return new Promise(function(resolve){
            function r(data){
                resolve({issignin:issignin,uid:uid,gettime:gettime,data:data});
            }
            if(Date.now()-gettime>10*60*1000 || mustcheck){
                usersign.check(getinfo).then(r).catch(r);
            }else{
                r({message:"usecache"});
            }
        });
    };
    
    let privates={
        checkvar() {
            if(gettime===0){
                
            }
        },
        getcache() {
            return privates.tryparse(sessionStorage.usersign)
        },
        checkcache() {
            let cache=privates.getcache();
            if(!(privates.empty(cache)||cache===false)&&gettime<cache.gettime){
                issignin=cache.issignin;
                uid=cache.uid;
                gettime=cache.gettime;
                return true;
            }
            return false;
        },
        setcache() {
            sessionStorage.usersign=JSON.stringify({
                issignin: issignin,
                uid: uid,
                gettime: gettime
            });
        },
        empty(str){
            if(str==="" || typeof str === "undefined"){
                return true;
            }
            return false;
        },
        tryparse(jsonstr,usepromise=false){
            if(!usepromise){
                try{
                    return (JSON.parse(jsonstr));
                }catch(err){
                    //console.log(err);
                    return false;
                }
            }
            return new Promise((resolve,reject)=>{
                try{
                    resolve(JSON.parse(jsonstr));
                }catch(err){
                    reject(err);
                }
            });
        }
    };
    
    usersign.check=function(getinfo=false) {
        let thisArg=this;
        return new Promise((resolve, reject) => {
            fetch(
                "//www.bplewiki.top/php/user/issignin.php"+(getinfo?"?getinfo=1":""),
                {method: "POST"}
            ).then(function(response){
                return response.json();
            }).then(function(result){
                if (result.success) {
                    issignin = true; 
                    uid = result.uid;
                    gettime = Date.now();
                    privates.setcache();
                    resolve(result.user); 
                } else {
                    issignin = false; 
                    uid = undefined;
                    gettime = Date.now();
                    privates.setcache();
                    reject(new Error("未登录")); 
                }
                usersign.trigger("aftercheck",{response:result});
            }).catch(function(err){
                reject(err);
            });
        });
    };
    /*
    usersign.autocheck=function(){
      return new Promise(function(resolve,reject){
          let thisArg=this;
          document.addEventListener('visibilitychange', function() {
            if (document.visibilityState !== 'visible') {
                return;
            }
            if(privates.checkcache()){
                if(usersign.issignin){
                    resolve();
                }else{
                    reject(new Error());
                }
            }
          });
      })
    };
    */
    usersign.events={};
    usersign.on=function(event,callback){
        usersign.events[event]=usersign.events[event]||[];
        usersign.events[event].push(callback);
    };
    usersign.trigger=function(event,obj){
        usersign.events[event]=usersign.events[event]||[];
        usersign.events[event].forEach(function(f){
            f(obj);
        })
    };
    
    privates.checkcache();
    usersign.create=function(check,getinfo){
        if(getinfo){
            return usersign.check(getinfo);
        }
    };
    document.addEventListener('visibilitychange', function() {
        if (document.visibilityState !== 'visible') {
            return;
        }
        privates.checkcache();
    });
    
    return usersign;
})();
