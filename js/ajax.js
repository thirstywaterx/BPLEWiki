function ajax(options){
  let xhr;
  if (window.XMLHttpRequest){
    xhr=new XMLHttpRequest();
  }else{
    xhr=new ActiveXObject("Microsoft.XMLHTTP");
  }
  xhr.onreadystatechange=function(){
    if (xhr.readyState==4 && xhr.status==200){
      options.success(this.responseText);
    }else if(xhr.readyState==4){
      if(options.fail){
        options.fail(this.statusText);
      }else{
        console.log(this.statusText);
      }
    }
  }
  xhr.open(options.method||"GET",options.url,true);
  if(options.beforeSend){
    options.beforeSend(xhr);
  }
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
  return xhr;
}