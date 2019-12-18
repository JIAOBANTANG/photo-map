var normalm1 = L.tileLayer.chinaProvider('Geoq.Normal.Map', {
    maxZoom: 12,
    minZoom: 4
});
var normalm2 = L.tileLayer.chinaProvider('Geoq.Normal.PurplishBlue', {
    maxZoom: 12,
    minZoom: 4
});
var normalm3 = L.tileLayer.chinaProvider('Geoq.Normal.Gray', {
    maxZoom: 12,
    minZoom: 4
});
var normalm4 = L.tileLayer.chinaProvider('Geoq.Normal.Warm', {
    maxZoom: 12,
    minZoom: 4
});
var normalm5 = L.tileLayer.chinaProvider('Geoq.Theme.Hydro', {
    maxZoom: 12,
    minZoom: 4
});
var normalMap = L.tileLayer.chinaProvider('Google.Normal.Map', {
    maxZoom: 20,
    minZoom: 4
}),
    satelliteMap = L.tileLayer.chinaProvider('Google.Satellite.Map', {
        maxZoom: 20,
        minZoom: 4
    });
    var normal = L.layerGroup([normalm1,satelliteMap, normalm2, normalm3, normalm4, normalm5]);
var baseLayers = {
    "地图": normalm1,
    "影像": satelliteMap,
    "午夜蓝": normalm2,
    "灰色": normalm3,
    "暖色": normalm4,
    "水系": normalm5
}
var map = L.map("map", {
    center: [39.907658, 116.404347],
    zoom: 12,
    layers: [normalm1],
    zoomControl: false
});
L.control.layers(baseLayers, null).addTo(map);
L.control.zoom({
    zoomInTitle: '放大',
    zoomOutTitle: '缩小'
}).addTo(map);

var photoLayer = L.photo.cluster().on('click', function (evt) {
    var photo = evt.layer.photo,
        template = `<div id='layer-photos-map'>
        <img src='{thumbnail}' viewer-src='{nondes}'/>
		<div style="padding:10px 0 5px 0">
            <img src='{avatar}' style="display: inline-block;width:30px;border-radius: 50%;vertical-align:bottom" alt="">
            <span class='address'>{name}</span>
        </div>
        <div class='mianshutext'>{event}</div>
        <p class='address'>{address}<br>({data_time})</p>
        <a href='javascript:;' onclick='delPhoto({id})'>抹掉此足迹</a>
        &nbsp;&nbsp;
        <a href='javascript:;' onclick='miaoShu(this)' data-id='{id}' data-content='{event}';>文字描述</a>
        </a>&nbsp;&nbsp;<a href='{url}' download=''>下载</a>
        </div>`;
    if (photo.video && (!!document.createElement('video').canPlayType('video/mp4; codecs=avc1.42E01E,mp4a.40.2'))) {
        template = '<video controls onclick="showVideo(this)" width="300" ><source src="{video}" type="video/mp4"/></video>';
//        template = `<video
//         id="my-player"
//         class="video-js"
//         controls
//         preload="auto"
//         poster="http://cdn.treelo.xin/avatar.png"
//         data-setup='123'>
//       <source src="{video}" type="video/mp4"></source>
// </video>
//         <script>
//         var player = videojs('my-player');
//         </script>
// `
    };
    evt.layer.bindPopup(L.Util.template(template, photo), {
        className: 'leaflet-popup-photo',
        minWidth: 300
    }).openPopup();
    var viewer = new Viewer(document.querySelector('#layer-photos-map'), {
        url:'viewer-src'
    });
});
if (data.rows.length > 0) {
    photoLayer.add(data.rows).addTo(map);
    map.fitBounds(photoLayer.getBounds());
}
//注意：导航 依赖 element 模块，否则无法进行功能性操作
layui.use('element', function () {
    var element = layui.element;
});
//加载框
var loding;
function addPhoto() {
    layui.use('layer', function () {
        var layer = layui.layer;
        layer.open({
            type: 1 //Page层类型
            , skin: 'class-uploads'
            , area: ['800px', '600px']
            , title: '记录生活'
            , shade: 0.4 //遮罩透明度
            , maxmin: true //允许全屏最小化
            , anim: 1 //0-6的动画形式，-1不开启
            , content: `<div class="layui-upload">
        <button type="button" class="layui-btn layui-btn-normal" id="testList">请选择图片</button> 
        <!--<span style="color:red;">！禁止中文图片名</span>-->
        <a style="color:#358d3a;" target="_brank" href="https://lbs.amap.com/console/show/picker">高德地图定位获取</a>
        <input type="text" class="layui-input datetimes" id="datetimes" placeholder="选择器,格式:Y-m-d H:m:s">
        <div class="layui-upload-list">
          <table class="layui-table">
            <thead>
              <tr><th>文件名</th>
                <th>预览</th>
              <th>大小</th>
              <th>状态</th>
              <th>操作</th>
            </tr></thead>
            <tbody id="demoList"></tbody>
          </table>
        </div>
        <button type="button" class="layui-btn" id="testListAction">开始上传</button>
	    <span class="" style="color:red;">涉及多图大文件上传时，请勿重复点击上传按钮</span>        
      </div>`
        });
        layui.use('laydate', function () {
            var laydate = layui.laydate;
            //日期时间选择器
            laydate.render({
                elem: '#datetimes'
                , type: 'datetime'
            });
        });
    });
    layui.use('upload', function () {
        var $ = layui.jquery
            , upload = layui.upload;
        //多文件列表示例
        var demoListView = $('#demoList')
            , uploadListIns = upload.render({
                elem: '#testList'
                , url: 'upload💕index'
                , accept: 'file'
                , multiple: true
                , auto: false
                , exts: 'jpg|JPG|jpeg|png|gif'
                , bindAction: '#testListAction'
                , choose: function (obj) {
                    // console.log(obj)
                    var files = this.files = obj.pushFile(); //将每次选择的文件追加到文件队列
                    var that = this;
                    //刷新页面
                    document.querySelector('.layui-layer-close').onclick = function () {
                        location.reload();
                    }

                    //给来个加载框
                    document.querySelector('#testListAction').onclick = function () {
                        let files = document.querySelector('#demoList').innerHTML;
                        if (files) {
                            loding = layer.load();
                        }
                    }
                    //读取本地文件
                    obj.preview(function (index, file, result) {
                        var tr = $(['<tr id="upload-' + index + '">'
                            , '<td ><div class="file-name">' + file.name + '</div></td>'
                            , '<td><img src="' + result + '" alt="' + file.name + '" class="layui-upload-img"></td>'
                            , '<td>' + (file.size / 1014).toFixed(1) + 'kb</td>'
                            , '<td>等待上传</td>'
                            , '<td>'
                            , '<p class="demo-reload-p layui-hide gaode-url" >图片EXIF数据缺失</p>'
                            , '<p class="demo-reload-p-lng-and-lat gaode-url layui-hide">请填入经纬度，和拍摄时间</p>'
                            , '<input class="layui-input demo-reload-lng-and-lat layui-hide"  type="text" name="lng" lay-verify="lng" autocomplete="off" placeholder="经纬度,用', '号隔开">'
                            , '<input class="layui-input demo-reload-time layui-hide" type="text" name="time" lay-verify="time" autocomplete="off" placeholder="拍摄时间">'
                            // ,'<input type="text" class="layui-input datetimes" id="test5" placeholder="yyyy-MM-dd HH:mm:ss">'
                            , '<button class="layui-btn layui-btn-xs demo-reload layui-hide">重传</button>'
                            , '<button class="layui-btn layui-btn-xs layui-btn-danger demo-delete">删除</button>'
                            , '</td>'
                            , '</tr>'].join(''));
                        //单个重传
                        tr.find('.demo-reload').on('click', function () {
                            var tr = demoListView.find('tr#upload-' + index)
                                , tds = tr.children();
                            let lngAndLat = tds.eq(4).find('.demo-reload-lng-and-lat').val(); //经纬度
                            let time = tds.eq(4).find('.demo-reload-time').val(); //时间
                            if (lngAndLat) {
                                // file = file[index];
                                let param = new FormData(); //创建form对象
                                param.append('lng-and-lat', lngAndLat);
                                param.append('time', time);
                                param.append('file', file, file.name);
                                let config = {
                                    headers: { 'Content-Type': 'multipart/form-data' }
                                };  //添加请求头
                                axios.post('/upload💕index', param, config)
                                    .then(function (response) {
                                        if (response.data.code == 2000) { //上传成功
                                            var tr = demoListView.find('tr#upload-' + index)
                                                , tds = tr.children();
                                            tds.eq(3).html('<span style="color: #5FB878;">上传成功</span>');
                                            tds.eq(4).html("拍摄于" + response.data.address); //清空操作
                                            return delete that.files[index]; //删除文件队列已经上传成功的文件
                                        }
                                    });
                            } else {
                                tds.eq(4).find('.demo-reload-p-lng').removeClass('layui-hide'); //显示提示
                                tds.eq(4).find('.demo-reload-p-lat').removeClass('layui-hide'); //显示提示
                            }

                        });
                        //删除
                        tr.find('.demo-delete').on('click', function () {
                            delete files[index]; //删除对应的文件
                            tr.remove();
                            uploadListIns.config.elem.next()[0].value = ''; //清空 input file 值，以免删除后出现同名文件不可选
                        });

                        demoListView.append(tr);
                    });
                }
                , done: function (res, index, upload) {
                    //关闭加载框
                    layer.close(loding);
                    if (res.code == 200) { //上传成功
                        var tr = demoListView.find('tr#upload-' + index)
                            , tds = tr.children();
                        tds.eq(3).html('<span style="color: #5FB878;">上传成功</span>');
                        tds.eq(4).html("拍摄于" + res.address); //清空操作
                        return delete this.files[index]; //删除文件队列已经上传成功的文件
                    }
                    if (res.code == 401) {
                        var tr = demoListView.find('tr#upload-' + index)
                            , tds = tr.children();
                        tds.eq(3).html('<span style="color: #FF5722;">上传失败</span>');
                        tds.eq(4).find('.demo-reload-p').removeClass('layui-hide'); //显示重传
                        tds.eq(4).find('.demo-reload-p-lng-and-lat').removeClass('layui-hide'); //显示重传
                        tds.eq(4).find('.demo-reload-lng-and-lat').removeClass('layui-hide'); //显示重传
                        tds.eq(4).find('.demo-reload-time').removeClass('layui-hide'); //显示重传
                        tds.eq(4).find('.demo-reload').removeClass('layui-hide'); //显示重传
                    }
                    if (res.code == 404) {
                        var tr = demoListView.find('tr#upload-' + index)
                            , tds = tr.children();
                        tds.eq(3).html('<span style="color: #FF5722;">' + res.msg + '</span>');
                        tds.eq(4).html('禁止操作'); //禁止操作
                    }
                }
                , error: function (index, upload) {
                    layer.close(loding);
                }
            });
    });
};

function friend(){
    layui.use('layer', function () {
        var layer = layui.layer;
        let content = '';
        let text = '';
        let love ='';
        if(love_data!==''){
            love = love_data.split(",");
            text = '我亦飘零久，寻此同归人。偕度烂漫日,执手抚生尘。'+love[2]+',足迹交汇';
            content = `
                 <div class="my">
                   <img src="${avatar}"
                     alt="" title="${name}💕${love[0]}">
                     <span>💕</span>
                   <img id="friend_avatar" src="${love[1]}"
                     alt="" title="${love[0]}💕${name}">
                 </div>
               `
        }else{
            text = '关联TA,只要告诉你，就证明想和你关联在一起';
            content= `
                    <div style="margin-top:10px" class="my">
                    <img title="目前还孤身一人" src="${avatar}"
                alt="" title=""><br>
                <input style="width:260px;color:hotpink;border: 0px solid #ccc;padding: 7px 0px;border-radius: 3px;padding-left:5px;box-shadow: inset 0 1px 1px rgba(0,0,0,.075);box-shadow: 0 0 8px hotpink" value="你的关联密钥为:${love_code}" ><br>
                <input style="width:220px;color:skyblue;border: 0px solid #ccc;padding: 7px 0px;margin:20px 0 0 0;border-radius: 3px;padding-left:5px;box-shadow: inset 0 1px 1px rgba(0,0,0,.075);box-shadow: 0 0 8px skyblue" placeholder='请输入对方关联密钥' id='to_love' name="to_love">
                    </div>
                    <div class="btn">
                    <a  href="javascript:;" onclick="bind()" class="bind" >关联TA</a>
                    </div>`
        }
        layer.open({
            type: 1 //Page层类型
            , skin: 'class-friend'
            , area: ['800px', '600px']
            , title: text
            , shade: 0.1 //遮罩透明度
            , maxmin: false //允许全屏最小化
            , anim: 1 //0-6的动画形式，-1不开启
            , content: content
        });
    });
}

function bind(){
    var to_love = document.querySelector('#to_love');
    if(to_love.value==''){
        layer.msg('对方的关联密钥不能为空哦', { icon: 5, anim: 6 });
    }else{
        axios.post('/user💕bind', {
            my_code:love_code,
            to_code:to_love.value
        })
        .then(function (response) {
            if (response.data.code == 200) {
                layer.msg(response.data.msg, { icon: 1, anim: 1 }, function () {
                    location.reload()
                });
            } else {
                layer.msg(response.data.msg, { icon: 5, anim: 6 });
            }
    });
    }
}

function delPhoto(fid) {
    console.log(fid)
    layui.use('layer', function () {
        layer.open({
            title: '你确定要删除吗?',
            skin: 'class-del'
            , offset: 'auto',
            content: '<img width="300" src="/images/wuwu.gif" alt="">'
            , btn: ['狠心删除', '我在想想', '还是不了']
            , yes: function (index, layero) {
                //按钮【按钮一】的回调
                //刷新页面
                console.log('jajaj')
                axios.post('/index💕delete', {
                    fid: fid,
                })
                    .then(function (response) {
                        if (response.data.code == 200) {
                            layer.msg(response.data.msg, { icon: 1, anim: 1 }, function () {
                                location.reload()
                            });
                        } else {
                            layer.msg(response.data.msg, { icon: 5, anim: 6 });
                        }
                    });
            }
            , btn2: function (index, layero) {
                //按钮【按钮二】的回调
                //return false 开启该代码可禁止点击该按钮关闭
            }
            , btn3: function (index, layero) {
                //按钮【按钮三】的回调
                //return false 开启该代码可禁止点击该按钮关闭
            }
            , cancel: function () {
                //右上角关闭回调
                //return false 开启该代码可禁止点击该按钮关闭
            }
        });
    });
}

//描述
function miaoShu(e) {
    let id = e.getAttribute('data-id');
    let content = e.getAttribute('data-content');
    layui.use('layer', function () {
        layer.open({
            type: 1,
            title: '能记录下来的还有文字哦~',
            area: ['600px', '450px'],
            skin: 'class-miaoShu'
            , offset: 'auto',
            content: `<textarea name="miaoshu" class="layui-textarea" id="miaoshu">` + content + `</textarea><button onclick="addMiaoShu(` + id + `)" class="layui-btn">提交文字</button>`
        });
    });

}

function addMiaoShu(id) {
    let content = document.querySelector('#miaoshu').value;
    axios.post('/index💕addmiaoshu', {
        fid: id,
        content: content
    })
        .then(function (response) {
            if (response.data.code == 200) {
                layer.msg(response.data.msg, { icon: 1, anim: 1 },function(){
                    document.querySelector('.mianshutext').innerHTML=content
                });
                setTimeout(function(){
                    layer.closeAll()
                },1000);
            } else {
                layer.msg(response.data.msg, { icon: 5, anim: 6 });
            }
        });
}