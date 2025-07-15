<!DOCTYPE html>
<html lang="en">
  <head>
    <!-- Title -->
    <title>Casiguro IMS</title>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="x-ua-compatible" content="ie=edge">

    <!-- Favicon-->
    <link rel="shortcut icon" href="{{asset('assets/public/img/casiguroLogo.png')}}">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Template -->
    <link rel="stylesheet" href="{{asset('assets/public/graindashboard/css/graindashboard.css')}}">
    <link rel="stylesheet" href="{{asset('assets/public/css/login.css')}}">

    <style>
      @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap');

      * {
          margin: 0;
          padding: 0;
          box-sizing: border-box;
          font-family: 'Poppins', sans-serif;
      }

      body {
    position: relative;
    overflow: hidden;
    height: 100vh;
    max-width: 100%;
    outline: none;
    direction: ltr;
    display: flex;
    justify-content: center;
    align-items: center;
    /* Lighter gradient for more visible image */
    background: linear-gradient(to bottom, rgba(241,236,222,0.4), rgba(213,236,236,0.2)),
                url("{{asset('assets/public/img/Casiguran.jpg')}}") no-repeat center center/cover;
}
     .main-login {
     background: linear-gradient(180deg, #F6F1E9 15.5%,rgb(76, 211, 216) 54.5%,rgb(70, 230, 203) 82%);
     background: linear-gradient(to bottom, #fff, rgba(0, 255, 195, 0.34)), url('background.jpg') no-repeat center center/cover;
    background-position-x: 0%, center;
    background-position-y: 0%, center;
    background-repeat: repeat, no-repeat;
    background-size: auto, cover;
    background-position: center center;
    background-repeat: no-repeat;
    background-size: cover;
    height: 100vh;
    width: 100%;
    }

      .background {
          margin: 0;
          width: 100%;
          height: 100vh;
          display: flex;
          justify-content: center;
          align-items: center;
      }

      .login-container {
          background: rgba(255, 255, 255, 0.3);
          padding: 30px;
          border-radius: 10px;
          backdrop-filter: blur(10px);
          text-align: center;
          box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
      }

      button:hover {
    background: #007bff; /* Changed from orange to blue */
    /* Or use yellow: background: #ffe066; */
}

      .card-login {
          position: relative; /* Ensure spans are confined to this container */
          overflow: hidden;
          width: 100%;
        max-width: 350px;    /* Set a maximum width for the card */
        min-width: 320px;    /* Optional: minimum width for responsiveness */
        min-height: 450px; /* Prevent spans from overflowing outside the card */
      }

      .k span {
          position: absolute;
          top: -120px;
          height: 50px;
          width: 50px;
          z-index: -1;
          animation: animate 10s linear infinite;
      }

      .k span:nth-child(1) {
        left: 60px;
        animation-delay: 0.6s;
        width: 50px; 
        height: 50px;
        background-image: url("{{ asset('assets/public/img/UPS.png') }}");
        background-size: cover;
        background-position: center;
        border-radius: 50%; 
    }

    .k span:nth-child(2) {
        left: 60%;
        animation-delay: 3s;
        width: 60px; 
        height: 60px; 
        background-image: url("{{ asset('assets/public/img/BAG.png') }}"); 
        background-size: cover; 
        background-position: center;  
        border-radius: 50%; 
    }
    .k span:nth-child(3) {
        left: 20%;
        animation-delay: 2s;
        width: 50px;
        height: 50px; 
        background-image: url("{{ asset('assets/public/img/SCAN.png') }}"); 
        background-size: cover; 
        background-position: center;
        border-radius: 50%; 
}
.k span:nth-child(4) {
    left: 30%;
    animation-delay: 5s;
    width: 80px;
    height: 80px; 
    background-image: url("{{ asset('assets/public/img/KEY.png') }}");
    background-size: cover; 
    background-position: center; 
}
.k span:nth-child(5) {
    left: 40%;
    animation-delay: 1s;
    width: 50px; 
    height: 50px; 
    background-image: url("{{ asset('assets/public/img/DESK.png') }}"); 
    background-size: cover; 
    background-position: center; 
    border-radius: 50%; 
}
.k span:nth-child(6) {
    left: 50%;
    animation-delay: 7s;
    width: 70px; 
    height: 70px; 
    background-image: url("{{ asset('assets/public/img/SIG.png') }}"); 
    background-size: cover; 
    background-position: center; 
    border-radius: 50%; 
}
.k span:nth-child(7) {
    left: 60%;
    animation-delay: 6s;
    width: 70px;
    height: 70px; 
    background-image: url("{{ asset('assets/public/img/PRINT.png') }}"); 
    background-size: cover; 
    background-position: center; 
}

      .k span:nth-child(8) {
          left: 70%;
          animation-delay: 8s;
          width: 70px;
          height: 70px; 
          background-image: url("{{ asset('assets/public/img/MOUSE.png') }}"); 
          background-size: cover; 
          background-position: center; 
      }

      .k span:nth-child(9) {
          left: 80%;
          animation-delay: 6s;
          width: 50px;
          height: 50px;
          background-image: url("{{ asset('assets/public/img/EXTERNAL.png') }}"); 
          background-size: cover; 
          background-position: center; 
      }
      .k span:nth-child(10) {
          left: 90%;
          animation-delay: 4s;
          width: 50px;
          height: 50px; 
          background-image: url("{{ asset('assets/public/img/SERVER.png') }}"); 
          background-size: cover; 
          background-position: center; 
      }

      /* .btn-block-login-1 {
          display: block;
          width: 50%;
          padding: 10px;
          background-color: #007bff; /* Changed from orange to blue 
          color: white;
          border: none;
          border-radius: 5px;
          cursor: pointer;
      } */

      .btn-block-login:hover {
       background-color: white; /* Darker blue on hover */
       color:  #007bff;            /* Optional: yellow text on hover */
}

      @keyframes animate {
          0% {
              transform: translateY(0) rotate(0deg);
              opacity: 1;
          }
          80% {
              opacity: 0.7;
          }
          100% {
              transform: translateY(800px) rotate(360deg);
              opacity: 0;
          }
      }
    </style>
  </head>

  <body>
    <main class="main-login">
      <div class="content-login">
        <div class="container-fluid-login pb-5">
          <div class="row justify-content-md-center">
            <div class="card-wrapper col-12 col-md-4 mt-5">
              <div class="brand text-center mb-3">
              <a href="/"><img src="{{asset('assets/public/img/CasiguroInc.png')}}" width="50%" height="50%" alt="logo"></a>
              </div>
              <div class="card-login">
							<div class="card-body-login">
								<h4 class="card-title-login">Casiguro Inventory Management System</h4>

                 {{-- ALERTS --}}
                  @if (session('Login_Sucessfully'))
                      <div class="alert alert-success alert-dismissible fade show mt-2" role="alert">
                          {{ session('Login_Sucessfully') }}
                          <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                              <span aria-hidden="true">&times;</span>
                          </button>
                      </div>
                  @endif

                  @if (session('withErrors'))
                      <div class="alert alert-danger alert-dismissible fade show mt-2" role="alert">
                          {{ session('withErrors') }}
                          <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                              <span aria-hidden="true">&times;</span>
                          </button>
                      </div>
                  @endif

                  @if ($errors->any())
                      <div class="alert alert-danger alert-dismissible fade show mt-2" role="alert">
                          <ul class="mb-0">
                              @foreach ($errors->all() as $error)
                                  {{ $error }}
                              @endforeach
                          </ul>
                          <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                              <span aria-hidden="true">&times;</span>
                          </button>
                      </div>
                  @endif
                  {{-- END ALERTS --}}


                <div class="modal fade" id="loginSuccessModal" tabindex="-1" role="dialog" aria-labelledby="loginSuccessModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                  <div class="modal-content">
                    <div class="modal-body text-center">
                      <h5 class="mb-3">Successfully login!</h5>
                      <div class="spinner-border text-success" role="status">
                        <span class="sr-only">Loading...</span>
                      </div>
                      <p class="mt-2">Redirecting to dashboard...</p>
                    </div>
                  </div>
                </div>
              </div>

								<form method="POST" action="{{ route('login') }}">
                  @csrf <!-- CSRF token for security -->
                  <div class="form-group-login">
                        <input id="employee_number" type="text" class="form-control-login" name="employee_number" required autofocus placeholder="Employee Number">
                  </div>
                                
                 <div class="form-group-login" style="position: relative;">
                    <input id="password" type="password" class="form-control-login" name="password" required placeholder="Password" style="padding-right: 40px;">
                    <span class="toggle-password" toggle="#password"
                          style="position: absolute; top: 50%; right: 15px; transform: translateY(-50%); cursor: pointer; color: #888;">
                        <i class="fa-solid fa-eye"></i>
                    </span>
                </div>

                  <!-- <div class="input-container">
                  <input placeholder="Enter password" type="password">

                  <span>
                    <svg stroke="currentColor" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                      <path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" stroke-width="2" stroke-linejoin="round" stroke-linecap="round"></path>
                      <path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" stroke-width="2" stroke-linejoin="round" stroke-linecap="round"></path>
                    </svg>
                  </span>
                </div> -->
                                
                  <div class="form-group-login no-margin">
                    <button type="submit" class="btn btn-primary-login btn-block-login">
                             Login
                    </button>
                    </div>
                  </form>
							</div>
						</div>
              <footer class="footer mt-3-1">
                <div class="container-fluid-login">
                  <div class="footer-content text-center small-1">
                  <span class="text-muted-login">&copy; Casiguro Enterprises, Inc.</span>
                  </div>
                </div>
              </footer>
            </div>
          </div>
        </div>
      </div>

      
    </main>

  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script>
    $(document).on('click', '.toggle-password', function() {
        let input = $($(this).attr('toggle'));
        let icon = $(this).find('i');
        if (input.attr('type') === 'password') {
            input.attr('type', 'text');
            icon.removeClass('fa-eye').addClass('fa-eye-slash');
        } else {
            input.attr('type', 'password');
            icon.removeClass('fa-eye-slash').addClass('fa-eye');
        }
    });
    </script>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    @if (session('Login_Sucessfully'))
        $(document).ready(function() {
            $('#loginSuccessModal').modal('show');
            setTimeout(function() {
                window.location.href = "{{ url('/dashboard') }}";
            }, 1800);
        });
    @endif
    </script>

    <script src="{{asset('assets/public/graindashboard/js/graindashboard.js')}}"></script>
    <script src="{{asset('assets/public/graindashboard/js/graindashboard.vendor.js')}}"></script>
  </body>
</html>