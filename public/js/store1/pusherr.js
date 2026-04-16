  import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

    var pusher = new Pusher('011aee0c99753166aef2', {
      cluster: 'ap2'
    });

    var channel = pusher.subscribe('my-channel');
    // channel public  donot degine in file channel.wroute
    channel.bind('my-event', function(data) {
        console.log(data)
      alert(JSON.stringify(data));  
    
    });
