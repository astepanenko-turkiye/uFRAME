/**
 * TODO: set initiating javascript functions for urls, for instance:
 * "console/photo-upload":"initUploadSkuId()"
 */
const initScriptsData={
}

function jsonFetch(url,params,callback,reject,headers={}) {

    let _o={
        headers: Object.assign({}, {
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        }, headers),
        method: "POST"
    };

    if(params) {
        _o["body"]=JSON.stringify(params);
    }

    fetch(url, _o)
    .then(response => response.json())
    .then(data => callback(data))
    .catch(error => {

        if(typeof reject === 'function') {
            reject(error);
        }

    });
}

function MessagePackFetch(url,params,callback,reject,headers={}) {

    let _o={
        headers: Object.assign({}, {
            'Accept': 'application/x-msgpack',
            'Content-Type': 'application/x-msgpack'
        }, headers),
        method: "POST"
    };

    if(params) {
        _o["body"]=MessagePack.encode(params);
    }

    fetch(url, _o)
    .then(response => {
        return response.arrayBuffer();
    })
    .then(arrayBuffer => {

        const data=MessagePack.decode(new Uint8Array(arrayBuffer));

        if(typeof callback === 'function') {
            callback(data);
        }

    })
    .catch(error => {

        if(typeof reject === 'function') {
            reject(error);
        }

    });

}

function trim(str, chars) { return str.trim().split(chars).filter(Boolean).join(chars) }

function replaceDomElement(responseHtml, selector, elId, hash, callback) {

    let newEl=new DOMParser().parseFromString(responseHtml,"text/html").querySelector(selector);

    newEl.setAttribute("data-hash", hash);

    let oldEl=document.querySelector(selector);

    let el=document.getElementById(elId);

    if(el) el.setAttribute("data-hash", hash);

    document.body.replaceChild(newEl, oldEl);

    if(callback) eval(callback);

}

window.onpopstate=function(e) {

    window.isPopped=true; setTimeout(function() { window.isPopped=false; },2000);

    if(e.state) {

        let func=e.state["func"];

        if(func) eval(func);

        document.title=e.state["pageTitle"];

    }

};

function getInitScripts(url) {

    let initScriptStr=null, initScriptArray=[];

    let pathname_arr=trim(url,"/").split("/");

    for(let i=0; i<pathname_arr.length; i++) {

        if(pathname_arr[i] in initScriptsData) initScriptArray.push(initScriptsData[pathname_arr[i]]);

    }

    if(initScriptArray.length) initScriptStr=initScriptArray.join(";");

    return initScriptStr;

}

function fetchUrl(elId,url,callback=null,makePushToHistory=true) {

    if(!callback) callback=getInitScripts(url);

    const el=document.getElementById(elId);

    let hash; if(el) hash=el.getAttribute("data-hash");

    jsonFetch(
        url,
        null,
        function(response) {

            if(response["success"]) {

                window.scrollTo({ top: 0, behavior: 'smooth' });

                if(el) el.classList.add("active");

                let mainEl=document.querySelector('main');

                const data_hash=mainEl.getAttribute("data-hash");

                let rerender=true; if(data_hash===hash && hash===response["hash"]) rerender=false;

                document.title=response["title"];

                if(rerender) {

                    replaceDomElement(response["html"], "main", elId, response["hash"], callback);

                    if(makePushToHistory) {

                        let evalStr="fetchUrl('"+elId+"','"+url+"',"+(callback ? "'"+callback+"'" :"null")+",false)";

                        window.history.pushState(
                            { func: evalStr, pageTitle: response["title"] },
                            "",
                            url
                        );

                    }

                }

            }

        },
        null,
        {
            'X-Requested-With': 'XMLHttpRequest',
            'Actual-Request-Type': 'GET'
        }
    );

}

document.addEventListener("click", function(e) {

    let targetEl, targetUrl;

    if(e.target.tagName==="A") {
        targetEl=e.target; targetUrl=e.target.getAttribute("href");
    } else {

        let aRelated=e.target.getAttribute("data-a-related");

        if(aRelated) {
            let a=document.getElementById(aRelated);

            if(a) {
                targetEl=a; targetUrl=a.getAttribute("href");
            }
        }

    }

    if(targetUrl && targetUrl.indexOf("#")!==0) {

        e.preventDefault();
        e.stopPropagation();

        let trimmedTargetUrl=trim(targetUrl,"/");
        let trimmedPathname=trim(window.location.pathname,"/");

        if(trimmedTargetUrl===trimmedPathname) return true;

        let elId=targetEl.getAttribute("id");
        if(!elId) { elId=Date.now(); targetEl.setAttribute("id", elId); console.error("no id"); }

        fetchUrl(elId,targetUrl);

        return true;

    }

    let id=e.target.getAttribute("id");
    let classList=e.target.classList;

    /**
     * TODO: handle other clicks
     */

}, false);

if(!window["firstRequestHappened"]) {

    let callback=getInitScripts(document.location.pathname);

    if(callback) eval(callback);

    let evalStr="fetchUrl('body','"+document.location.pathname+"',"+(callback ? "'"+callback+"'" :"null")+",false)";

    window.history.replaceState(
        { func: evalStr, pageTitle: document.title },
        "",
        document.location.href
    );

    window["firstRequestHappened"]=true;

}
