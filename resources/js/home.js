var $ = require('./jquery');

require('./jquery.backstretch.min');

var images = [];
images.push('/images/home/11838844_1681415315426002_7943262589252491928_o.jpg');
images.push('/images/home/11807186_1681415108759356_2012583516355331398_o.jpg');
images.push('/images/home/11864864_1686734318227435_7395144794963996504_o.jpg');
images.push('/images/home/12232709_10154371401844488_4064204128653645885_o.jpg');
images.push('/images/home/12247931_10154368627714488_1818566135957547328_o.jpg');
$('header').backstretch(images, {duration: 5000, fade: 750});
