 <style>
     .custom-search-btn {
         background: linear-gradient(135deg, #043a5f, #0973ba) !important;
         color: #ffffff !important;
         border: none;
         font-weight: 500;
         border-radius: 6px;

         transition:
             background-color 0.3s ease,
             box-shadow 0.3s ease,
             transform 0.2s ease;
     }

     .custom-search-btn:hover {
         background: linear-gradient(135deg, #043a5f, #0973ba) !important;
         color: #ffffff !important;

         box-shadow: 0 6px 16px rgba(13, 110, 253, 0.35);
         transform: translateY(-2px);
     }

     .custom-search-btn:active {
         transform: translateY(0);
         box-shadow: 0 3px 8px rgba(13, 110, 253, 0.25);
     }
 </style>

 <button type="button" id="searchform" class="btn custom-search-btn">{{ __('common.search') }}</button>
