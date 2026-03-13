 <style>
     .custom-reset-btn {
         background-color: red;
         color: #ffffff !important;
         border: none;
         font-weight: 500;
         border-radius: 6px;

         transition:
             background-color 0.3s ease,
             box-shadow 0.3s ease,
             transform 0.2s ease;
     }

     .custom-reset-btn:hover {
         background-color: rgb(224, 30, 30) !important;
         color: #ffffff !important;

         box-shadow: 0 6px 16px rgba(108, 117, 125, 0.35);
         transform: translateY(-2px);
     }

     .custom-reset-btn:active {
         transform: translateY(0);
         box-shadow: 0 3px 8px rgba(108, 117, 125, 0.25);
     }
 </style>

 <button type="reset" id="resetform" class="btn custom-reset-btn reset_form">Reset</button>
