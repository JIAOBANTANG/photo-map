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
        template = "<div id='layer-photos-map'><img src='{thumbnail}' viewer-src='{nondes}'/><div class='mianshutext'>{event}</div><p class='address'>{name}</p><p class='address'>{address}({data_time})</p><a href='javascript:;' onclick='delPhoto({id})'>抹掉此足迹</a>&nbsp;&nbsp;<a href='javascript:;' onclick='miaoShu(this)' data-id='{id}' data-content='{event}';>文字描述</a></a>&nbsp;&nbsp;<a href='{url}' download=''>下载</a></div>";
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
    // layui.use('layer', function () {
    //     layer.photos({
    //         photos: '#layer-photos-map'
    //         , anim: 0//0-6的选择，指定弹出图片动画类型，默认随机（请注意，3.0之前的版本用shift参数）
    //     });
    // });
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
        <a class="gaode-url" target="_brank" href="https://lbs.amap.com/console/show/picker">高德地图定位获取</a>
        <input type="text" class="layui-input datetimes" id="datetimes" placeholder="这是个时间选择器,选好复制，格式:Y-m-d H:m:s">
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
                                // let that = this;

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
                                // file.lng = lng;
                                // file.lat = lat;
                                // file.time = time;
                                // console.log(obj)
                                // obj.upload(index, file);
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
                    if (res.code == 2000) { //上传成功
                        var tr = demoListView.find('tr#upload-' + index)
                            , tds = tr.children();
                        tds.eq(3).html('<span style="color: #5FB878;">上传成功</span>');
                        tds.eq(4).html("拍摄于" + res.address); //清空操作
                        return delete this.files[index]; //删除文件队列已经上传成功的文件
                    }
                    if (res.code == 4001) {
                        var tr = demoListView.find('tr#upload-' + index)
                            , tds = tr.children();
                        tds.eq(3).html('<span style="color: #FF5722;">上传失败</span>');
                        tds.eq(4).find('.demo-reload-p').removeClass('layui-hide'); //显示重传
                        tds.eq(4).find('.demo-reload-p-lng-and-lat').removeClass('layui-hide'); //显示重传
                        tds.eq(4).find('.demo-reload-lng-and-lat').removeClass('layui-hide'); //显示重传
                        tds.eq(4).find('.demo-reload-time').removeClass('layui-hide'); //显示重传
                        tds.eq(4).find('.demo-reload').removeClass('layui-hide'); //显示重传
                    }
                    if (res.code == 4004) {
                        var tr = demoListView.find('tr#upload-' + index)
                            , tds = tr.children();
                        tds.eq(3).html('<span style="color: #FF5722;">' + res.msg + '</span>');
                        tds.eq(4).html('禁止操作'); //禁止操作
                    }
                }
                , error: function (index, upload) {

                }
            });
    });
};
function addVideo() {
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
        <button type="button" class="layui-btn layui-btn-normal" id="testList">请选择视频</button> 
        <span style="color:red;">！禁止中文图片名</span> 
        <a class="gaode-url" target="_brank" href="https://lbs.amap.com/console/show/picker">高德地图定位获取</a>
        <input type="text" class="layui-input datetimes" id="datetimes" placeholder="这是个时间选择器,选好复制，格式:yyyy-MM-dd HH:mm:ss">
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
	    <span class="gaode-url">涉及多图大文件上传时，请勿重复点击上传按钮</span>        
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
                , url: 'upload💕upVideo'
                , accept: 'video'
                , multiple: false
                , auto: false
                , exts: 'mov'
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
                        var url = URL.createObjectURL(file)
                        var tr = $(['<tr id="upload-' + index + '">'
                            , '<td ><div class="file-name">' + file.name + '</div></td>'
                            , '<td><video src ='+url+' id="aa" hight="140" width = "140" controls="controls"></video></td>'
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
                                // let that = this;
                                axios.post('/upload💕upVoideo', param, config)
                                    .then(function (response) {
                                        if (response.data.code == 2000) { //上传成功
                                            var tr = demoListView.find('tr#upload-' + index)
                                                , tds = tr.children();
                                            tds.eq(3).html('<span style="color: #5FB878;">上传成功</span>');
                                            tds.eq(4).html("拍摄于" + response.data.address); //清空操作
                                            return delete that.files[index]; //删除文件队列已经上传成功的文件
                                        }
                                    });
                                // file.lng = lng;
                                // file.lat = lat;
                                // file.time = time;
                                // console.log(obj)
                                // obj.upload(index, file);
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
                    if (res.code == 2000) { //上传成功
                        var tr = demoListView.find('tr#upload-' + index)
                            , tds = tr.children();
                        tds.eq(3).html('<span style="color: #5FB878;">上传成功</span>');
                        tds.eq(4).html("拍摄于" + res.address); //清空操作
                        return delete this.files[index]; //删除文件队列已经上传成功的文件
                    }
                    if (res.code == 4001) {
                        var tr = demoListView.find('tr#upload-' + index)
                            , tds = tr.children();
                        tds.eq(3).html('<span style="color: #FF5722;">上传失败</span>');
                        tds.eq(4).find('.demo-reload-p').removeClass('layui-hide'); //显示重传
                        tds.eq(4).find('.demo-reload-p-lng-and-lat').removeClass('layui-hide'); //显示重传
                        tds.eq(4).find('.demo-reload-lng-and-lat').removeClass('layui-hide'); //显示重传
                        tds.eq(4).find('.demo-reload-time').removeClass('layui-hide'); //显示重传
                        tds.eq(4).find('.demo-reload').removeClass('layui-hide'); //显示重传
                    }
                    if (res.code == 4004) {
                        var tr = demoListView.find('tr#upload-' + index)
                            , tds = tr.children();
                        tds.eq(3).html('<span style="color: #FF5722;">' + res.msg + '</span>');
                        tds.eq(4).html('禁止操作'); //禁止操作
                    }
                }
                , error: function (index, upload) {

                }
            });
    });
};

function friend(){
    // let content = '';
    // axios.post('/user💕getFriend')
    // .then(function (response) {
    //     console.log(response.data)
    //         if (response.data.code == 2000) {
    //             if(response.data.info.my){
    //                 if(response.data.info.my.f_is==0){
    //                     //你邀请好友了但人家还没同意
    
    //                 }elseif(response.data.info.my.f_is==1){
    //                     //你邀请好友了人家也同意了
    
    //                 }else{
    //                     //你邀请好友了但人家拒绝你了
    //                 }
    //             }else{
    //                 //你没邀请任何人，
    //             }

    //             if(response.data.info.my){

    //             }else{
    //                 //还没人邀请你
    //             }
                
    //         } else {
    //             layer.msg('网站里没有这个人，禁止幻想', { icon: 5, anim: 6 });
    //         }
    //     });
    layui.use('layer', function () {
        var layer = layui.layer;
        // if(fid==0){
        //      contents = `
        //      <div class="find">
        //        <div class="wenzi">
        //          <div>在</div>
        //          <div>此</div>
        //          <div>觅</div>
        //          <div>到</div>
        //          <div>TA</div>
        //        </div>
        //        <div class="you">
        //          <input type="text" onchange="find(this)" name="you">
        //        </div>
        //      </div>
        //      <div class="my">
        //        <img src="`+avatar+`"
        //          alt="">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        //        <img id="friend_avatar" src=""
        //          alt="">
        //      </div>
        //      <div class="btn"> 
        //        <a  href="javascript:;" onclick="" class="bind" >确定发起绑定请求</a>
        //    </div>`;
        // }else{
        //      contents = ``
        // }
        layer.open({
            type: 1 //Page层类型
            , skin: 'class-friend'
            , area: ['800px', '600px']
            , title: '关联TA'
            , shade: 0.1 //遮罩透明度
            , maxmin: false //允许全屏最小化
            , anim: 1 //0-6的动画形式，-1不开启
            , content: `<div class="wenzi">
                     <div>这</div>
                     <div>事</div>
                     <div>情</div>
                     <div>被</div>
                     <div>搁</div>
                     <div>置</div>
                     <div>在</div>
                     <div>了</div>
                     <div>心</div>
                     <div>里</div>
                   </div>`
        });
    });
    // console.log('hahah')
}
//关联相关先关掉
// function find(e){
//     axios.post('/user💕getFriendInfo', {
//         name: e.value,
//     })
//     .then(function (response) {
//         console.log(response.data)
//             if (response.data.code == 2000) {
//                 let friend_img = document.querySelector('#friend_avatar');
//                 friend_img.setAttribute('src',response.data.avatar);
//                 let a = document.querySelector(".bind");
//                 a.setAttribute('onclick','bind('+response.data.fid+')')
//             } else {
//                 layer.msg('网站里没有这个人，禁止幻想', { icon: 5, anim: 6 });
//             }
//         });
// }
// function bind(fid){
//     axios.post('/user💕bindFriend', {
//         fid: fid,
//     })
//     .then(function (response) {
//         console.log(response.data)
//             if (response.data.code == 2000) {
//                 layer.msg('网站里没有这个人，禁止幻想', { icon: 1, anim: 6 });    

//             } else {
//                 layer.msg('网站里没有这个人，禁止幻想', { icon: 5, anim: 6 });
//             }
//         });
// }


// function showVideo(e){
//     console.log(e);
// }

// function openVideoPage() {
//     var entity = {};// 传输数据的实体变量
//     var flag = false;// 标识，用来判断是否选择文件，选择的文件大小是否大于零
//     var _ctx;
//     var coversflag = false;// 这个可能没用，但是不想删除。
//         layui.use('layer', function () {
//         var layer = layui.layer;
//         var $ = layui.jquery
        
//     layer.open({// 将要打开一个弹出层
//         type: 1,
//         title: "基本信息",
//         content: ugcUploadPage.value,
//         closeBtn: 2,
//         area: ["700px", "600px"],
//         shift: 5,
//         moveType: 1,
//         btn: ["提交", "关闭"],
//         success: function (layero, index) {// 打开弹出层时，发生了什么？
//             layui.use(['upload', 'form'], function () {// layui加载模块
//                 var form = layui.form;
//                 var upload = layui.upload;
//                 form.on('select(lang)', function (data) {// 获取lang
//                     entity.lang = data.value;
//                 });

//                 var uploadInst = upload.render({//执行实例
//                     elem: '#video'
//                     , url: _ctx + "ugcManagement/uploadGVideo"
//                     , accept: 'video'
//                     , exts: 'mp4|mov'
//                     , data: entity
//                     , drag:true
//                     , auto: false
//                     , bindAction: ".layui-layer-btn0"
//                     , choose: function (obj) {// 选择文件之后，提交文件之前，你想干什么？
//                         obj.preview(function (index, file, result) {
//                             if (file.size > 0) {// 判断文件大小
//                                 flag = true;
//                                 layer.msg("Wait for me to disappear, forget I can be good");// 此段话，用来拖延用户点击提交按钮的时间，因为不知道covers是否返回
//                             }
//                             var url = URL.createObjectURL(file)
//                             if (url != null) {// 显示video标签，用于预览
//                                 $('.video').css("display", "block");
//                             }
//                             $('#aa').attr("src", url);


//                             var a = document.getElementById("aa");

//                             var timesRun = 0;
//                             var timer = setInterval(function () {// 定时器获取时长，，，，定时器只一次
//                                 timesRun += 1;
//                                 if (timesRun === 1) {
//                                     clearInterval(timer);// 关闭定时器
//                                 }
//                                 entity.contentLen = parseInt(a.duration);// 获取视频时长，如果不使用定时器，获取时长可能是NAN，所以必须定时器
//                                 console.log(parseInt(a.duration));
//                                 // 视频缩略图-先获取video对象-用canvas画图，返回imageSrc，返回的是base64编码-然后解码，生成二进制blob文件，提交二进制文件到后台。如果这里不使用定时器，也获取不到图片。
//                                 var video = document.getElementById('aa');
//                                 var canvas = document.createElement('canvas');
//                                 var ctx = canvas.getContext('2d');
//                                 var imgHeight = video.videoHeight;
//                                 var imgWidth = video.videoWidth;
//                                 ctx.drawImage(video, 0, 0, imgWidth, imgHeight);
//                                 var imgSrc = canvas.toDataURL('image/png');
//                                 var binary = atob(imgSrc.split(',')[1]);
//                                 var array = [];
//                                 for (var i = 0; i < binary.length; i += 1) {
//                                     array.push(binary.charCodeAt(i));
//                                 }
//                                 var blob = new Blob([new Uint8Array(array)], { type: 'image/png' });
//                                 var u = URL.createObjectURL(blob)
//                                 var formData = new FormData();
//                                 formData.append('file', blob);
//                                 $.ajax({
//                                     url: _ctx + "pic/uploadImage",
//                                     crossDomain: true,
//                                     data: formData,
//                                     dataType: 'json',
//                                     type: 'POST',
//                                     contentType: false,
//                                     processData: false,
//                                     success: function (data) {
//                                         if (data != null) {
//                                             entity.covers = JSON.stringify(data[0]);// 返回covers的JSON字符串
//                                             coversflag = true; //这个没啥用、不想删、请善待。
//                                         }
//                                     }
//                                 });
//                             }, 1000);
//                         });
//                     }
//                     , done: function (res) {// 上传完成之后，干些事。
//                         if (res.code == "1") {
//                             layer.closeAll('loading');
//                             layer.closeAll();
//                             layer.msg("success");
//                         } else {
//                             layer.closeAll('loading');
//                             layer.msg("error");
//                         }

//                     }
//                 });
//             });
//         },
//         yes: function (layero, index) {// 提交按钮，做些什么？
//             layui.use('form', function () {
//                 var form = layui.form;
//                 form.on('select(lang)', function (data) {
//                     var lang = data.value;
//                     console.log(data.value);
//                 });
//             });
//             var title = $("#title").val();
//             entity.title = title;
//             console.log(entity)
//             if (flag) {
//                 layer.load();
//             } else {
//                 layer.msg("文件不能为空！！！");
//             }
//         }
//     });
// });
// }

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
                        if (response.data.code == 2000) {
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
            if (response.data.code == 2000) {
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


// var options = {};

// var player = videojs('my-player', options, function onPlayerReady() {
//   videojs.log('Your player is ready!');

//   // In this context, `this` is the player that was created by Video.js.
//   this.play();

//   // How about an event listener?
//   this.on('ended', function() {
//     videojs.log('Awww...over so soon?!');
//   });
// });