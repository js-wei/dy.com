//重新书写提示  
jQuery.extend(jQuery.validator.messages, {
    required: "必选字段",
    remote: "请修正该字段",
    email: "请输入正确格式的电子邮件",
    url: "请输入合法的网址",
    date: "请输入合法的日期",
    dateISO: "请输入合法的日期 (ISO).",
    number: "请输入合法的数字",
    digits: "只能输入整数",
    creditcard: "请输入合法的信用卡号",
    equalTo: "请再次输入相同的值",
    accept: "请输入拥有合法后缀名的字符串",
    maxlength: jQuery.validator.format("请输入一个 长度最多是 {0} 的字符串"),
    minlength: jQuery.validator.format("请输入一个 长度最少是 {0} 的字符串"),
    rangelength: jQuery.validator.format("请输入 一个长度介于 {0} 和 {1} 之间的字符串"),
    range: jQuery.validator.format("请输入一个介于 {0} 和 {1} 之间的值"),
    max: jQuery.validator.format("请输入一个最大为{0} 的值"),
    min: jQuery.validator.format("请输入一个最小为{0} 的值")
});
//自定义扩展  
jQuery.validator.addMethod("isMobile", function(value, element) {
    var length = value.length;
    var mobile = /^(((13[0-9]{1})|(15[0-9]{1})|(18[0-9]{1}))+\d{8})$/;
    return this.optional(element) || (length == 11 && mobile.test(value));
}, "手机格式不正确");
jQuery.validator.addMethod("isEmail", function(value,element){
    var email=/^(\w)+(\.\w+)*@(\w)+((\.\w+)+)$/;
    return this.optional(element)||email.test(true);
},"邮箱格式不正确");
jQuery.validator.addMethod("filterDBcom", function(value, element) {
    var command = /select|update|delete|insert|declare|dbcc|alter|drop|creat|backup|add|set|open|close|exec|count|’|"|=|;|>|<|%/i;
    return this.optional(element) || !(command.test(value));
}, "不能包含sql特殊字符");
jQuery.validator.addMethod("filterHTML", function(value, element) {
    var chrnum = /<[^>]+>/;
    return this.optional(element) || !(chrnum.test(value));
}, "不用包含html字符");
jQuery.validator.addMethod("isIp", function(value, element) {
    var chrnum = /^(\d{1,2}|1\d\d|2[0-4]\d|25[0-5])\.(\d{1,2}|1\d\d|2[0-4]\d|25[0-5])\.(\d{1,2}|1\d\d|2[0-4]\d|25[0-5])\.(\d{1,2}|1\d\d|2[0-4]\d|25[0-5])$/;
    return this.optional(element) || chrnum.test(value);
}, "ip格式不正确");
jQuery.validator.addMethod("haveBlank", function(value, element) {
    var is = value.indexOf(" ") >= 0?false:true;
    return this.optional(element) || is;
}, "不能包含空格");

//错误提示信息创建什么标签<label>xxxx</label>  
$.validator.setDefaults({
    errorElement : "label"
});