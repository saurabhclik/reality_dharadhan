$(document).ready(function () 
{
    setTimeout(function () 
    {
        $('.loader').fadeOut('slow');
    }, 500);

    $('#togglePassword').on('click', function () 
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

    $('.dealer-specific').hide();
    $('input[name="leadType"]').on('change', function () 
    {
        if ($(this).val() === 'dealer') 
            {
            $('.dealer-specific').show();
            $('.customer-specific').hide();
        }
        else 
        {
            $('.dealer-specific').hide();
            $('.customer-specific').show();
        }
    });

    const x = document.getElementById("demo");
    function setCookie(name, value, days) 
    {
        const date = new Date();
        date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
        const expires = "expires=" + date.toUTCString();
        document.cookie = name + "=" + value + ";" + expires + ";path=/";
    }

    function getCookie(name) 
    {
        const decodedCookie = decodeURIComponent(document.cookie);
        const cookies = decodedCookie.split(';');
        for (let i = 0; i < cookies.length; i++) 
        {
            const c = cookies[i].trim();
            if (c.indexOf(name + "=") === 0) 
            {
                return c.substring(name.length + 1, c.length);
            }
        }
        return "";
    }

    function getLocation() 
    {
        if (navigator.geolocation) 
        {
            navigator.geolocation.getCurrentPosition(
                success,
                error,
                { timeout: 10000 }
            );
        } 
        else 
        {
            x.innerHTML = "Geolocation is not supported by this browser.";
        }
    }

    function success(position) 
    {
        const latitude = position.coords.latitude;
        const longitude = position.coords.longitude;
        setCookie('startLocation', `Latitude: ${latitude}, Longitude: ${longitude}`, 7);
        x.innerHTML = `Location saved: Latitude: ${latitude}, Longitude: ${longitude}`;
        console.log("Location fetched and saved to cookies.");
    }

    function error(err) 
    {
        if (err.code === 1) 
        {
            setCookie('locationPermission', 'denied', 7);
            x.innerHTML = "Location permission denied. Please allow access in browser settings.";
        } 
        else if (err.code === 2) 
        {
            x.innerHTML = "Location unavailable. Please try again later.";
        } 
        else if (err.code === 3)
        {
            x.innerHTML = "Location request timed out.";
        }
    }
    const startLocation = getCookie('startLocation');
    const locationPermission = getCookie('locationPermission');

    if (!startLocation) 
    {
        if (!locationPermission || locationPermission === 'denied') 
        {
            getLocation();
        } 
        else 
        {
            x.innerHTML = "Fetching location...";
        }
    } 
    else 
    {
        x.innerHTML = startLocation;
    }
    $('#retryLocation').on('click', function () 
    {
        getLocation();
    });
});
