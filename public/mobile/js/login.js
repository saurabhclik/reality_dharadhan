$(document).ready(function() 
{
    function createParticles() 
    {
        const particlesContainer = $('#particles');
        const particleCount = 15;
        
        for (let i = 0; i < particleCount; i++) 
        {
            const size = Math.random() * 10 + 5;
            const posX = Math.random() * 100;
            const delay = Math.random() * 15;
            const duration = Math.random() * 10 + 10;
            
            $('<div class="particle"></div>').css({
                width: `${size}px`,
                height: `${size}px`,
                left: `${posX}%`,
                top: '100vh',
                animationDelay: `${delay}s`,
                animationDuration: `${duration}s`
            }).appendTo(particlesContainer);
        }
    }
    createParticles();
    $('#togglePassword').on('click', function() 
    {
        const password = $('#password');
        const icon = $(this).find('i');
        
        if (password.attr('type') === 'password') 
        {
            password.attr('type', 'text');
            icon.removeClass('fa-eye').addClass('fa-eye-slash');
        } 
        else 
        {
            password.attr('type', 'password');
            icon.removeClass('fa-eye-slash').addClass('fa-eye');
        }
    });
    
    $('form').on('submit', function() 
    {
        $('.btn-app').html('<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Signing in...');
        $('.btn-app').prop('disabled', true);
    });

    $('.app-logo').on('mousemove', function(e) 
    {
        const x = e.pageX - $(this).offset().left;
        const y = e.pageY - $(this).offset().top;
        
        const centerX = $(this).width() / 2;
        const centerY = $(this).height() / 2;
        
        const angleX = (y - centerY) / 10;
        const angleY = (centerX - x) / 10;
        
        $(this).css('transform', `scale(1.05) rotateX(${angleX}deg) rotateY(${angleY}deg) translateZ(10px)`);
        $(this).find('img').css('transform', `scale(1.1)`);
    });
    
    $('.app-logo').on('mouseleave', function() 
    {
        $(this).css('transform', 'scale(1) rotateX(0) rotateY(0) translateZ(0)');
        $(this).find('img').css('transform', 'scale(1)');
    });
    setTimeout(function() 
    {
        $('.flash').remove();
    }, 5000);
});