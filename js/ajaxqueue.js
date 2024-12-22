var ajaxqueue={
    iswait:false,
    all:[],
    add(options){
        this.all.push(options);
        if(!this.iswait){
            this.run();
        }
    },
    run(){
          let options=this.all.shift();
          this.iswait=true;
          let xhr;
          if (window.XMLHttpRequest){
            xhr=new XMLHttpRequest();
          }else{
            xhr=new ActiveXObject("Microsoft.XMLHTTP");
          }
          xhr.onreadystatechange=function(){
            if (xhr.readyState==4 && xhr.status==200){
              options.success(this.responseText);
              if(this.all.length>0){
                  ajaxqueue.run();
              }else{
                  ajaxqueue.iswait=false;
              }
            }else if(xhr.readyState==4 && options.fail){
              options.fail(this.statusText);
            }
          }
          xhr.open(options.method||"GET",options.url,true);
          if(options.send){
            if(options.sendtype==="fd"){
                let formdata=new FormData();
                for(let [key,value] of options.send){
                    formdata.appear(key,value);
                }
                xhr.send(formdata);
            }else if(options.sendtype==="fd2"){
                let formdata=new FormData(options.send);
                xhr.send(formdata);
            }else if(options.sendtype==="json"){
                xhr.setRequestHeader("Content-Type", "application/text/json;charset=UTF-8");
                xhr.send(JSON.stringify(options.send));
            }else{
                xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded;charset=UTF-8");
                xhr.send(options.send);
            }
          }else{
            xhr.send();
          }
          return xhr;
    }
};
ajaxqueue.p=function(options){
  return new Promise((resolve,reject)=>{
      let xhr=new XMLHttpRequest();
      xhr.onreadystatechange=function(){
        if (xhr.readyState==4 && xhr.status==200){
            resolve(this.responseText);
        }else if(xhr.readyState==4){
            reject(this.responseText||this.statusText);
        }
      }
      xhr.open(options.method||"GET",options.url,true);
      if(options.beforeSend){
        options.beforeSend(xhr);
      }
      xhr.timeout=options.timeout||15*1000;
      xhr.ontimeout=options.ontimeout||function(){
        reject("timeout")
      };
      if(options.send){
        if(options.sendtype==="fd"){
            let formdata=new FormData();
            for(let [key,value] of options.send){
                formdata.appear(key,value);
            }
            xhr.send(formdata);
        }else if(options.sendtype==="fd2"){
            xhr.send(new FormData(options.send));
        }else if(options.sendtype==="send"){
            xhr.send(options.send);
        }else if(options.sendtype==="json"){
            xhr.send(JSON.stringify(options.send));
        }else{
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded;charset=UTF-8");
            xhr.send(options.send);
        }
      }else{
        xhr.send();
      }
  });
};