import axios from 'axios';

export default () => ({
    page: null,
    ajax: false,
    showPage: true,
    ajaxLoading: false,
    async initAction() {
        let link = window.landingUrl;
        let el = document.getElementById('renderedpanel');
        while (el == null) {
            await window.sleep(50);
            el = document.getElementById('renderedpanel');
        }
        this.$store.app.xpages[link] = el.innerHTML;
        history.pushState({href: link}, '', link);
        // axios.get(link, {data: {"x_mode": 'ajax'}} ).then(
        //     (r) => {
        //         this.$store.app.xpages = [];
        //         this.$store.app.xpages[link] = r.data;
        //         history.pushState({href: link}, '', link);
        //     }
        // ).catch(
        //     function (e) {
        //         console.log(e);
        //     }
        // );
    },
    historyAction(e) {
        if (e.state != undefined && e.state != null) {
            let link = e.state.href;
            this.showPage = false;
            this.ajaxLoading = true;
            if (this.$store.app.xpages[link] != undefined) {
                setTimeout(() => {
                    this.showPage = true;
                    this.page = this.$store.app.xpages[link];
                    this.$dispatch('pagechanged', {currentpath: link, currentroute: detail.route});
                    this.ajaxLoading = false;
                },
                    100
                );
            } else {
                setTimeout(() => {
                    this.showPage = true;},
                    100
                );
            }
        }
    },
    getQueryString(params) {
        let thelink = "";
            let keys = Object.keys(params);

            for (let j=0; j < keys.length; j++) {
                if (Array.isArray(params[keys[j]])) {
                    for (let x = 0; x < params[keys[j]].length; x++) {
                        thelink += keys[j]+'[]=' + params[keys[j]][x];
                        if (x < (params[keys[j]].length -1)) {
                            thelink += '&';
                        }
                    }
                } else {
                    thelink += keys[j]+'=' + params[keys[j]];
                }

                if (j < (keys.length - 1)) {
                    thelink += '&';
                }
            }
            return thelink;
    },
    fetchLink(detail) {
        let link = detail.link;
        let params = detail.params;
        let thelink = link;
        if (detail.params != null) {
            thelink += "?" + this.getQueryString(params);
        }
        // if (this.$store.app.xpages != undefined && this.$store.app.xpages[thelink] != undefined) {
        //     this.showPage = false;
        //     this.ajaxLoading = true;
        //     if (this.$store.app.xpages[thelink] != undefined) {
        //         setTimeout(() => {
        //             this.showPage = true;
        //             this.page = this.$store.app.xpages[thelink];
        //             this.$dispatch('pagechanged', {currentpath: link, currentroute: detail.route});
        //             this.ajaxLoading = false;
        //         },
        //             100
        //         );
        //     } else {
        //         setTimeout(() => {
        //             this.showPage = true;
        //             this.ajaxLoading = false;
        //         },
        //             100
        //         );
        //     }
        //     history.pushState({href: thelink}, '', thelink);
        // } else {
            this.$store.app.pageloading = true;
            // this.$dispatch('pageload');
            if (params != null) {
                params['x_mode'] = 'ajax';
            } else {
                params = {x_mode: 'ajax'};
            }
            this.ajaxLoading = true;
            axios.get(link, {params: params}).then(
                (r) => {
                    this.showPage = false;
                    this.ajax = true;
                    setTimeout(
                        () => {
                            // if(document.getElementById('renderedpanel') != null) {document.getElementById('renderedpanel').remove();}
                            document.getElementById('renderedpanel').innerHTML = r.data;
                            this.page = r.data;
                            this.showPage = true;
                            this.ajaxLoading = false;
                        },
                        100
                    );
                    if (this.$store.app.xpages == undefined || this.$store.app.xpages == null) {
                        this.$store.app.xpages = [];
                    }
                    this.$store.app.xpages[thelink] = r.data;
                    history.pushState({href: thelink}, '', thelink);
                    this.$store.app.pageloading = false;
                    // clearInterval(timer);
                    // timer = null;
                    // initialised = false;
                    this.$dispatch('pagechanged', {currentpath: link, currentroute: detail.route});
                }
            ).catch(
                function (e) {
                    console.log(e);
                }
            );
            // this.$store.app.pageloading = false;
        // }
    },
    // doSearch(detail) {
    //     let fullUrl = detail.url + '?';
    //     let keys = Object.keys(detail.params);
    //     keys.forEach((key) => {
    //         fullUrl += key + '=' + details.params[key];
    //     });
    //     console.log(fullUrl);
    //     this.fetchLink(fullUrl);
    // },
    resetPages() {
        this.$store.app.xpages = [];
    },
    formatted(e, n = 0) {
        return (e * 1).toFixed(n);
    },
    formatString(str, n = 10) {
        if (str.length > n) {
            return str.substring(0, n) + '...';
        }
        return str;
    },
    isEmail(email) {
        return String(email)
            .toLowerCase()
            .match(
            /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/
            );
    }
});