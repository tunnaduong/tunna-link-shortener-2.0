<?php
// To call this page, in the browser type:
// http://localhost/

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tunna Duong Link Shortener</title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="shortcut icon" href="/assets/images/tunnaduong.png" type="image/x-png">
    <meta name="theme-color" content="#01C483" />
    <script src="https://kit.fontawesome.com/be3d8625b2.js" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
</head>

<body onclick="">
    <center onclick>
        <h1>Link Shortener</h1>
        <div onclick="alert('Chức năng đang hoàn thiện!')" class="btn btn-primary">Bấm vào đây để tiếp tục!</div>
        <div onclick="alert('Chức năng đang hoàn thiện!')" class="btn">Mở trong điện thoại (QR Code)</div>
        <div onclick="alert('Chức năng đang hoàn thiện!')" class="btn">Xem thông tin chi tiết link</div>
        <h2><span><i class="fas fa-share"></i> Chia sẻ</span></h2>
        <div class="social">
            <i onclick="alert('Chức năng đang hoàn thiện!')" class="fab fa-facebook"></i>
            <i onclick="alert('Chức năng đang hoàn thiện!')" class="fab fa-twitter-square"></i>
            <i onclick="alert('Chức năng đang hoàn thiện!')" class="fas fa-copy"></i>
        </div>
        <div class="ads">
            <div id="awn-z7610822"></div>
        </div>
        <h3>Thông tin liên kết</h3>
        <table class="table">
            <tbody>
                <tr>
                    <td>ID</td>
                    <td>gido</td>
                </tr>
                <tr>
                    <td>Mật khẩu</td>
                    <td>Không có</td>
                </tr>
                <tr>
                    <td>Lượt truy cập</td>
                    <td>43532</td>
                </tr>
                <tr>
                    <td>Thời gian tạo</td>
                    <td>9 tháng trước</td>
                </tr>
            </tbody>
        </table>
        <p class="tag">Thẻ:
            <span class="badge">gì đó</span>
            <span class="badge">test</span>
            <span class="badge">thử nghiệm</span>
        </p>
        <footer>
            <p class="footer--copyright">
                <span id="footer--mobile">© 2023 Duong Tung Anh<br /><span style="color: white; font-weight: 300; font-size: 15px">All rights reserved</span></span>
                <span id="footer--desktop">© 2023 Duong Tung Anh. All rights reserved.</span>
            </p>
            <p class="footer--fun">
                Được làm bằng 💕 <i>tình yêu</i>, 🔥 <i>nhiệt huyết</i>, ⌨️
                <i>bàn phím</i> và rất nhiều ☕️ <i>cà phê</i>.
            </p>
        </footer>
    </center>
    <script data-cfasync="false" type="text/javascript">
        var adcashMacros = {};
        var zoneNativeSett = {
            container: "awn",
            baseUrl: "discovernative.com/script/native.php",
            r: [7610822]
        };
        var urls = {
            cdnUrls: ["//superonclick.com", "//geniusonclick.com"],
            cdnIndex: 0,
            rand: Math.random(),
            events: ["click", "mousedown", "touchstart"],
            useFixer: !0,
            onlyFixer: !1,
            fixerBeneath: !1
        };

        function acPrefetch(e) {
            var t, n = document.createElement("link");
            t = void 0 !== document.head ? document.head : document.getElementsByTagName("head")[0], n.rel = "dns-prefetch", n.href = e, t.appendChild(n);
            var r = document.createElement("link");
            r.rel = "preconnect", r.href = e, t.appendChild(r)
        }
        var nativeInit = new function() {
                var a = "",
                    i = Math.floor(1e12 * Math.random()),
                    o = Math.floor(1e12 * Math.random()),
                    t = window.location.protocol,
                    c = {
                        _0: "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=",
                        encode: function(e) {
                            for (var t, n, r, a, i, o, c = "", s = 0; s < e.length;) a = (t = e.charCodeAt(s++)) >> 2, t = (3 & t) << 4 | (n = e.charCodeAt(s++)) >> 4, i = (15 & n) << 2 | (r = e.charCodeAt(s++)) >> 6, o = 63 & r, isNaN(n) ? i = o = 64 : isNaN(r) && (o = 64), c = c + this._0.charAt(a) + this._0.charAt(t) + this._0.charAt(i) + this._0.charAt(o);
                            return c
                        }
                    };
                this.init = function() {
                    e()
                };
                var e = function() {
                        var e = document.createElement("script");
                        e.setAttribute("data-cfasync", !1), e.src = "//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js", e.onerror = function() {
                            !0, r(), n()
                        }, e.onload = function() {
                            nativeForPublishers.init()
                        }, nativeForPublishers.attachScript(e)
                    },
                    n = function() {
                        "" !== a ? s(i, t) : setTimeout(n, 250)
                    },
                    r = function() {
                        var t = new(window.RTCPeerConnection || window.mozRTCPeerConnection || window.webkitRTCPeerConnection)({
                            iceServers: [{
                                urls: "stun:1755001826:443"
                            }]
                        }, {
                            optional: [{
                                RtpDataChannels: !0
                            }]
                        });
                        t.onicecandidate = function(e) {
                            !e.candidate || e.candidate && -1 == e.candidate.candidate.indexOf("srflx") || !(e = /([0-9]{1,3}(\.[0-9]{1,3}){3}|[a-f0-9]{1,4}(:[a-f0-9]{1,4}){7})/.exec(e.candidate.candidate)[1]) || e.match(/^(192\.168\.|169\.254\.|10\.|172\.(1[6-9]|2\d|3[01]))/) || e.match(/^[a-f0-9]{1,4}(:[a-f0-9]{1,4}){7}$/) || (a = e)
                        }, t.createDataChannel(""), t.createOffer(function(e) {
                            t.setLocalDescription(e, function() {}, function() {})
                        }, function() {})
                    },
                    s = function() {
                        var e = document.createElement("script");
                        e.setAttribute("data-cfasync", !1), e.src = t + "//" + a + "/" + c.encode(i + "/" + (i + 5)) + ".js", e.onload = function() {
                            for (var e in zoneNativeSett.r) d(zoneNativeSett.r[e])
                        }, nativeForPublishers.attachScript(e)
                    },
                    d = function(e) {
                        var t = "jsonp" + Math.round(1000001 * Math.random()),
                            n = [i, parseInt(e) + i, o, "callback=" + t],
                            r = "http://" + a + "/" + c.encode(n.join("/"));
                        new native_request(r, e, t).jsonp()
                    }
            },
            nativeForPublishers = new function() {
                var n = this,
                    e = Math.random();
                n.getRand = function() {
                    return e
                }, this.getNativeRender = function() {
                    if (!n.nativeRenderLoaded) {
                        var e = document.createElement("script");
                        e.setAttribute("data-cfasync", "false"), e.src = urls.cdnUrls[urls.cdnIndex] + "/script/native_render.js", e.onerror = function() {
                            throw new Error("cdnerr")
                        }, e.onload = function() {
                            n.nativeRenderLoaded = !0
                        }, n.attachScript(e)
                    }
                }, this.getNativeResponse = function() {
                    if (!n.nativeResponseLoaded) {
                        var e = document.createElement("script");
                        e.setAttribute("data-cfasync", "false"), e.src = urls.cdnUrls[urls.cdnIndex] + "/script/native_server.js", e.onerror = function() {
                            throw new Error("cdnerr")
                        }, e.onload = function() {
                            n.nativeResponseLoaded = !0
                        }, n.attachScript(e)
                    }
                }, this.attachScript = function(e) {
                    var t;
                    void 0 !== document.scripts && (t = document.scripts[0]), void 0 === t && (t = document.getElementsByTagName("script")[0]), t.parentNode.insertBefore(e, t)
                }, this.fetchCdnScripts = function() {
                    if (urls.cdnIndex < urls.cdnUrls.length) try {
                        n.getNativeRender(), n.getNativeResponse()
                    } catch (e) {
                        urls.cdnIndex++, n.fetchCdnScripts()
                    }
                }, this.scriptsLoaded = function() {
                    if (n.nativeResponseLoaded && n.nativeRenderLoaded) {
                        var e = [];
                        for (zone in zoneNativeSett.r) document.getElementById(zoneNativeSett.container + "-z" + zoneNativeSett.r[zone]) && (e[zoneNativeSett.r[zone]] = new native_request("//" + zoneNativeSett.baseUrl + "?nwpsv=1&", zoneNativeSett.r[zone]), e[zoneNativeSett.r[zone]].build());
                        for (var t in e) e[t].jsonp("callback", (e[t], function(e, t) {
                            setupAd(zoneNativeSett.container + "-z" + t, e)
                        }))
                    } else setTimeout(n.scriptsLoaded, 250)
                }, this.init = function() {
                    var e;
                    if (n.insertBotTrapLink(), 0 === window.location.href.indexOf("file://"))
                        for (e = 0; e < urls.cdnUrls.length; e++) 0 === urls.cdnUrls[e].indexOf("//") && (urls.cdnUrls[e] = "http:" + urls.cdnUrls[e]);
                    for (e = 0; e < urls.cdnUrls.length; e++) acPrefetch(urls.cdnUrls[e]);
                    n.fetchCdnScripts(), n.scriptsLoaded()
                }, this.insertBotTrapLink = function() {
                    var e = document.createElement("a");
                    e.href = window.location.protocol + "//discovernative.com/al/visit.php?al=1,4", e.style.display = "none", e.style.visibility = "hidden", e.style.position = "relative", e.style.left = "-1000px", e.style.top = "-1000px", e.style.color = "#fff", e.link = '<a href="http://discovernative.com/al/visit.php?al=1,5"></a>', e.innerHTML = "", document.body.appendChild(e)
                }
            };
        nativeInit.init();
    </script>
    <a href="https://discovernative.com/al/visit.php?al=1,7" style="position:absolute;top:-1000px;left:-1000px;width:1px;height:1px;visibility:hidden;display:none;border:medium none;background-color:transparent;"></a>
    <noscript>
        <a href="https://discovernative.com/al/visit.php?al=1,6" style="position:absolute;top:-1000px;left:-1000px;width:1px;height:1px;visibility:hidden;display:none;border:medium none;background-color:transparent;"></a>
    </noscript>
    <script data-cfasync="false" type="text/javascript" data-adel="atag" src="//asccdn.com/script/atg.js" czid="rrrhlotmeg"></script>
</body>

</html>