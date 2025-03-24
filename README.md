Sbse phle mujhe projects setup krne ka code bhi de
Or aap mujhe jo bhi Model, migration controller etc. jo bhi denge unki commands bhi dena
migration or model ki wo commands dena jisme mere Model Migrations ek sath ban jaaye
Mujhe aap controller ka code de or usme sb kuch logics unke likh kr de
Sbhi ke liye admin ke pass show hide ka control hona chaihye kiskio kya data dikhana hai kya nhi.
Pura project api routes + bootstrap5 main rahega


Mujhe laravel main jo auth system banega api routes se usme mujhe api se hi banana hai sb kuch usme kuch aesa hoga ki wo properly secure hona chahiye usme multi role auth system hoga jisme unke khud ke alag alag dashbaord honge or users table main hi sbhi fileds honge register ke time pr name, email, password hi save hoga baaki ke fields null honge usme jo fileds null honge wo apni profile se update kr sakega.
Mera auth system or baaki ki files aesi honi chahiye ki hackers ke attacks se bach ske properly secure hona chahiye achhe se isse krke mujhe pura code de with blade file frontend ke sath main sanctum ke sath krege jo ki mera phle hi install hai
Main Focus: Secure API, Role-based access, SPA behavior, Protection from hacking attempts (XSS, CSRF, SQL Injection).

Database bhi uska crypted rahega jisse baad main koi smjh na ske na koi data ko kharab kr ske

Ye projects mera api ke sath ho or koi issue na aaye isme page load na ho isme background main api chale aesa krna hai ek ek steps ko

Mycrm ke name se laravel 12 main api routes ke sath main application web banani hai jisme salesperson ke leads, expenses, profile, kitni leads confirm huyi kitni nhi, quotation sb chij ki, or jitni bhi ek product ke liye salesperson ki chije hoti hai wo banani hai mujhe aap complete de kyunki mujhe real time update chahiye Jahan bhi ho jese Admin ne kuch change Kiya to sb jagah usi waqt ho jana chahiye, or sbhi ke graphs bhi chahiye taaki unhe dekh kr easily pta lg jaaye ki salesperson ne kya kya Kiya hai kb kb Kiya hai mujhe uski attandance ke sath live location bhi chahiye with calender jisme present, absent, leave, events all over sb kuch hoga

Iska mujhe aap sbhi ko migration, model or api routes ka uske sath jo bhi blade file banegi sbka pura or proper code de.

Isme mujhe pusher ka use nhi Krna kyunki wo paid hai mujhe bilkul free application banani hai or kuch bhi change ho kre uska notification bhi aaye or isme Mujhe aesa Krna hai ki ye smoothly work kre
Isme Jo salesperson or Admin ka number save hoga wo ek WhatsApp number hoga wo automatically number ko dekhe or salesperson ko message daal de subh dhopher saam ko ki uske kitne target hai kitne baaki hai or kitne pure ho gye hai kitne lost ho gye hai sb kuch leads ke target

* sbse phle mujhe ye krna hai ki login or registration ke liye ye role based hoga isme role (admin, salesperson, dealer, carpenter) hoga or jb regisration hoga to uska name email to aa hi jayega with role to hme uski baaki ki information bhi save krwani hai jese ki niche di gyi hai
    -  'employee_id',
        'phone',
        'photo',
        'whatspp number',
        'pin code',
        'address',
        'location',
        'designation',
        'date_of_joining',
        'status'

        ye saari informations bhi store krwani hai jb wo registration ho jayega to usme locaiion, attendance status, leave_type, holiday_reason, total_sales, holiday_reason, target_sales, conversion_rate, average_deal_size, performance_rating ye itni fileds hai jo wo baad main update hogi kyunki attandance ki to alag table banegi, sales activity ki alag se banegi jisme salesperosn ke target_sales or total_sales ka bhi rahega, or salesperson ke login hote hi uski live location update ho jayegi usi din ki attandance kahan se ki hai,


1. admin ke pass sbhi salesperson ke jaaye message ki kon se salesperson ke kitne target hai kitne pure kr liye hai or kitne baaki hai admin ke pass subh or saam ko jayege message aesa Krna hai


2. database management aesi krni hai ki sara data per day ka achhe se manage ho ske shi se or fastest work ho
Jyda time na le page load na ho sath ke sath sb kuch real time main ho jana chahiye.

4. Live location ka bhut achhe se dhyan rakhna hai ki salesperson kahan gya hai or kahan se work kiya hai sb kuch pet day ki update rhe admin ke pass

5. Mujhe admin+salesperson dono ka dashboard chahiye uske salesperson ka chart bhi chahiye sbhi ka alag alag jisko dekh kr easy se pta lg jaaye daily ka work ka or monthly kitna kiya hai kitna expense kiya hai quarterly kitna kiya hai wo sb chahiye mujhe.

6. Attandance management main kuch aesa ho ki salesperson ko apne dashboard pr top main ek calender dikhayi de usme unki present absent leave or uske jo bhi events hai wo dikhe alag alag color main or calender pr click krke hi events ko add kr ske wo aesa rakhna hoga.

7. Salesperson ka dashboard aesa hona chahiye ki sb chije smjh aaye or sb kuch usse dikhayi de or smoothly work kre na ki load hone main jyda time le.


Isme blade file ke liye bootstrap 5 ka use Krna hoga laravel 12 + route api + blade file + bootstrap 5 aur aese Krna hai ki android app banaye to usme bhi database yhi work kre proper chahe aap api banaye ya kese bhi kre dhyan rakhe ki Android+web dono pr work krega.

Mujhe ye crm kuch aesa krna hai ki isme mujhe hand to hand sb chije pta rhe salesperson ki wo kya kr rha hai kitna kiya hai kb kiya hai kitni leads leke aaya hai kitni confirm huyi hai kitni cancel or kya resion rhe hai unke or kuch aesa bhi ho ki page fastest ho or hand to hand result de mujhe unke chart bhi dikhe


Note :- Salesperson ka dashboard page alag rahega
        Admin ka dashboard page alag rahega
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

✅ Role-Based Authentication (Admin, Salesperson, Dealer, Carpenter)
✅ Live Location Tracking (Salesperson ke login hote hi)
✅ Leads & Sales Tracking (Kitni leads confirm, reject, lost, etc.)
✅ Real-Time Updates (Admin ya Salesperson ka data turant update ho)
✅ Notification System (WhatsApp API ya Free SMS Gateway ke sath)
✅ Performance & Sales Analytics (Graphs aur Charts ke sath)
✅ Attendance & Calendar Integration
✅ Android App Friendly API

ab mujhe frontend main UI banana hai bhut hi smooth or achha bananana hai jisme bootstrap5 ka use hoga or css ka use kr skte hai jrurat pdi to warna jyda jruri nhi hai lekin applications achhi or branded lagni chahiye UI aesa hona chahiye ki pura crm manage ho ske isme aesa krna hai chahe to aap cards ka use kr skte hai or isme view achha hona chahiye dekhne pr links bhi dene hai jo bhi unka whatsapp number save hoga usse hi whatsapp pr message ya reminder bhejne ke liye use krna hai

icons ka bhi use krna hai isme Isme add update delete ko lekar sb kuch hoga or bina page load huye real time hona chahiye sb kuch jese hi kuch update kiya ya add kiya wo sath ke sath hi page pr dikh jaaye ki kya kiya hai abhi sb kuch realtime hona chahiye isme api routes bana hi rakhe hai sbke hmne


 Laravel Project Setup Commands
 Run these commands in terminal to set up Laravel project
 1. Install Laravel
 composer create-project --prefer-dist laravel/laravel mycrm

 2. Navigate into the project folder
 cd mycrm

 3. Set up environment file
 cp .env.example .env

 4. Generate application key
 php artisan key:generate

 5. Install dependencies
 composer install

 6. Set up database in .env file
 DB_CONNECTION=mysql
 DB_HOST=127.0.0.1
 DB_PORT=3306
 DB_DATABASE=mycrm
 DB_USERNAME=root
 DB_PASSWORD=

 7. Run migrations
 php artisan migrate

 8. Install Laravel Sanctum for authentication
composer require laravel/sanctum
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
php artisan migrate

 9. Setup Storage Link
 php artisan storage:link

 10. Serve the application
 php artisan serve


 Ek baat or mere dimag main aayi hai ki agar salesperson ne uss din ki attandance lga di hai to dubara se na lga ske wo Sirf uss din ki leave daal ske jis din ki attandance na lge wo bydefault absent lge 10 ya 11 bje ke baad absent mark ho jaaye apne aap aese ho skta hai kya




1. Profile Information:

    Profile Image
    Basic Info ('employee_id',
        'phone',
        'photo',
        'whatspp number',
        'pin code',
        'address',
        'location',
        'designation',
        'date_of_joining',
        'status')
    About Me Section (Location, Department, Date of Birth)

2. Performance Tab:
    Monthly Performance Chart (Leads and Sales)
    Lead Status Distribution Chart
    Recent Achievements Table

3. Activity Tab:
    Timeline of User Activities
    Color-coded Icons
    Date and Time Stamps

4. Settings Tab:
    Email Notifications Toggle
    SMS Notifications Toggle
    Theme Selection (Light/Dark)

5. Modals for:
    Update Profile
    Change Password
    
6. Features:
    Responsive Design mobile interface web and app
    Chart.js Integration
    AJAX Form Submissions
    File Upload Support
    SweetAlert2 Notifications
    Form Validation
    Password Confirmation

    Isme blade file ke liye bootstrap 5 ka use Krna hoga laravel 12 + route api + blade file + bootstrap 5 aur aese Krna hai ki android app banaye to usme bhi database yhi work kre proper chahe aap api banaye ya kese bhi kre dhyan rakhe ki Android+web dono pr work krega.

    Mujhe ye crm kuch aesa krna hai ki isme mujhe hand to hand sb chije pta rhe salesperson ki wo kya kr rha hai kitna kiya hai kb kiya hai kitni leads leke aaya hai kitni confirm huyi hai kitni cancel or kya resion rhe hai unke or kuch aesa bhi ho ki page fastest ho or hand to hand result de mujhe unke chart bhi dikhe

    Mujhe aap controller ka code de or usme sb kuch logics unke likh kr de
    Sbhi ke liye admin ke pass show hide ka control hona chaihye kiskio kya data dikhana hai kya nhi.
    Pura project api routes + bootstrap5 main rahega

    ✅ Role-Based Authentication (Admin, Salesperson, Dealer, Carpenter)
    ✅ Live Location Tracking (Salesperson ke login hote hi)
    ✅ Leads & Sales Tracking (Kitni leads confirm, reject, lost, etc.)
    ✅ Real-Time Updates (Admin ya Salesperson ka data turant update ho)
    ✅ Notification System (WhatsApp API ya Free SMS Gateway ke sath)
    ✅ Performance & Sales Analytics (Graphs aur Charts ke sath)
    ✅ Attendance & Calendar Integration
    ✅ Android App Friendly API

    google map api key :- AIzaSyBl5N0v6zO372f3-RU-mSKNAMyN1Cu0Rzk

    whatspp number :- 8607807612

    salesperson :

    share leads(other brands) :- new lead aayi hai iska follow up leta rha lekin iske pass person work krne ke liye tayar nhi hai to other brand main jaakar kisi or ko de ske aesa krna hai.


    sabse phle salesperson ke attandance ko complete krna hai main focus yhi hai app or web ke liye sirf api.php se mtlb /api/ lgega jo response bhi dega or website ko view pr bhi dikhayega bilkul real time data ajax se kr skte hai with bearer token ka dhyan rakhe security ka
    mene sanctum api ka use kiya hai iska dhyan rakhe
    jo aapko dashboard dikha rakha hai view/frontend/salesperson/dashboard.blade.php and editpipline.blade.php se aesa krna hai or behatar achha krna hai proper crm system banana hai mujhe

    **** jb tk attandance na lagaye mtlb salesperson present na ho uss din to wo new lead or new expenses add na kr ske
    **** per person ka duty time alag rahega 9.30AM se 7.30PM uske baaad hamesha late mark hoga
    *****  check-in krne ke baad uski timeline ban kr dikh jaaye admin dashboard main salesperson ki jese google ki timeline dikhti hai per month ki datewise kb kahan gye the vese hi alag se page pr
    **** late aane pr whatspp number pr jo number save ho uspe autometically message chala jaaye aese krna hai isme
    **** check-out krega tb tk uss din ki map ki timeline admin ke pass rahegi uske baad ki nhi
    jb check-in krega fir se start ho jayegi wo aese krna hai real time map tracking chahiye


chalo ab frontend UI design krte hai mere pass kuch hai jese hi salesperson ke dashboard ke liye mene usme ek blade design de rakha hai usse dynmic krna hai kyunki meri api web or app dono pr work krni chahiye usme sb kuch kre or mere pass uske dashboard ke liye ek image hai aap wo dekh kr design lga skte hai kuch vesa admin ke liye bhi add kr skte hai



1. Profile Information:

    Profile Image
    Basic Info ('employee_id',
        'phone',
        'photo',
        'whatspp number',
        'pin code',
        'address',
        'location',
        'designation',
        'date_of_joining',
        'status')
    About Me Section (Location, Department, Date of Birth)

2. Performance Tab:
    Monthly Performance Chart (Leads and Sales)
    Lead Status Distribution Chart
    Recent Achievements Table

3. Activity Tab:
    Timeline of User Activities
    Color-coded Icons
    Date and Time Stamps

4. Settings Tab:
    Email Notifications Toggle
    SMS Notifications Toggle
    Theme Selection (Light/Dark)

5. Modals for:
    Update Profile
    Change Password
    
6. Features:
    Responsive Design mobile interface web and app
    Chart.js Integration
    AJAX Form Submissions
    File Upload Support
    SweetAlert2 Notifications
    Form Validation
    Password Confirmation

    
    ✅ Role-Based Authentication (Admin, Salesperson, Dealer, Carpenter)
    ✅ Live Location Tracking (Salesperson ke login hote hi)
    ✅ Leads & Sales Tracking (Kitni leads confirm, reject, lost, etc.)
    ✅ Real-Time Updates (Admin ya Salesperson ka data turant update ho)
    ✅ Notification System (WhatsApp API ya Free SMS Gateway ke sath)
    ✅ Performance & Sales Analytics (Graphs aur Charts ke sath)
    ✅ Attendance & Calendar Integration
    ✅ Android App Friendly API

kya aap aese kr skte hai sbhi controller ke code ke method main taaki data shi se unke dashboard pr pahunch ske
   // Always return JSON for API requests
            if ($request->wantsJson()) {
                return response()->json([
                    'is_checked_in' => $attendance && $attendance->check_in_time,
                    'is_checked_out' => $attendance && $attendance->check_out_time,
                    'status' => $attendance ? $attendance->status : null,
                    'check_in_time' => $attendance ? $attendance->check_in_time : null,
                    'check_out_time' => $attendance ? $attendance->check_out_time : null
                ]);
            }
        return view('admin.dashboard', compact());
        return view('salesperson.dashboard', compact());



    ***** admin dashboard pr attandance ka chart bhi dikhana hai per salesperson ke hisab se mtlb jo bhi uske pass salesperson aayege wo monthly, weekly and yearly teeno tarah ka with percentage ki kitna present tha kitna absent kitni leave thi with calendar ke sath dikhe
    **** admin ko ek access dena hai ki present salesperson ki locations dekh ske ki kahan kahan gya hai or kitne time kahan rha hai wo sb kuch or history bhi banni chahiye google ki tarah alag se
    **** admin ke pass salesperson ki sb kuch chije dikhe jo bhi hai uske events, task
    **** admin ke pass salesperson ka sb kuch show hide krne ka option hona chahiye ki usse kya dikhana hai kya nhi jis salesperson pr click kre usi ke table main se
    **** admin ke pass sbhi chijo ke chart banne chahiye or wo api se jsonwant main or compact se view pr dono pr ho ske

    sbhi ko design kre or api routes agar bane hai admin ke salesperson ke to unhe use kre agar nhi bne hai to aap bana kr controller main logics likhe fir unhi logics ko UI main lagaye shi se koi errors issue problem na rhe


    **** Lead ka task admin daal skta hai -> per day ki jese 10lead di usne 2 lead complete ki hai to baaki 8 lead pending dikha de uncomplete your target today leads

    **** sales ka task admin daal skta hai -> jese 20lakh ka diya or kiya 2lakh ka monthly

    Whatsapp sms jaaye attandance with status, lead task, sales task 

    sales force main target diya tha mothly, weekly, quarterly, Yealy


    jese hi month ki 1 tarik aaye plan lena hai ki iss month kya krege 

    lead plans, sales plan ka target denge -> option dene honge target diya tha mothly, weekly, quarterly, Yealy


    Meeting ka option -> reminder date pr

    reminder date nikal jaaye to uska status pending main rahega ne delete kr skta hai


 🛠 संभावित सुधार और सुझाव:
1️⃣ 🔍 डेटा वैलिडेशन और एरर हैंडलिंग में सुधार:

अभी getLocation() का इस्तेमाल कर रहे हैं, लेकिन अगर लोकेशन डेटा नहीं मिले तो Error Handling को और बेहतर किया जा सकता है।

checkIn() और checkOut() में लोकेशन वैलिडेशन को और स्ट्रॉन्ग किया जा सकता है।

2️⃣ 📅 वर्किंग आवर्स कैलकुलेशन:

php
Copy
Edit
'working_hours' => $attendance->check_in && $attendance->check_out
    ? Carbon::parse($attendance->check_in)->diffInHours(Carbon::parse($attendance->check_out)) 
    : 0,
अभी आपके export() फ़ंक्शन में working_hours को सही ढंग से कैलकुलेट नहीं किया जा रहा है। इसे diffInHours() के साथ कैलकुलेट करें।

3️⃣ 📊 ग्राफ और एनालिटिक्स जोड़ें:

उपस्थिति के आंकड़े दिखाने के लिए Chart.js या Google Charts का उपयोग करके डेटा को विज़ुअली प्रेजेंट करें।

4️⃣ 🔐 सुरक्षा सुधार:

Auth Middleware का उपयोग करें ताकि हर API कॉल में यह जांच हो कि उपयोगकर्ता वास्तव में लॉगिन है या नहीं।

SQL Injection से बचने के लिए Query Binding का उपयोग करें।

👨‍💻 अगले कदम:
✅ Attendance Dashboard बनाएं (जहां ग्राफ, स्टेट्स और महीने की रिपोर्ट दिखे)।
✅ Push Notifications जोड़ें (लेट आने पर सेल्सपर्सन को नोटिफिकेशन मिले)।
✅ GPS Location Accuracy सुधारें (फ्रंटेंड पर Google Maps API के साथ)।
