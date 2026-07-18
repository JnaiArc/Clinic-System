<?php
/**
 * SwiftCare Clinic - Smart Bilingual Chatbot Router Backend
 * Comprehensive layout matching official booking form functionalities.
 */

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['message'])) {
    echo json_encode(['reply' => 'Invalid Request']);
    exit();
}

$message = strtolower(trim($_POST['message']));
$message = preg_replace('/[.,\/#!$%\^&\*;:{}=\-_`~()]/', ' ', $message); 
$message = preg_replace('/\s+/', ' ', $message); // Normalize spaces
$reply = "";


//1.WORDPOOL DEFINITIONS

//Profanity/Curse Words Pool
$pool_profanity = [
    'gago', 'tanga', 'bobo', 'putanginamo', 'putangina', 'tangina', 'pukinangina', 
    'puta', 'kingina', 'pota', 'ulol', 'g@go', 'b0b0', 't@nga', 'kupal', 'pakyu', 
    'tarantado', 'siraulo', 'leche', 'buwisit', 'bwisit', 'putres', 'hudas', 
    'fuck', 'shit', 'asshole', 'bitch', 'bastard', 'dick', 'pussy', 'fucker', 
    'fucking', 'shitty', 'f*ck', 'sh*t', 'pake', 'paki', 'gaga', 'gugung', 'boba'
];

//Casual response ng clients
$pool_acknowledgments = [
    'ok', 'okay', 'noted', 'sige', 'gets', 'copy', 'alright', 'ahok', 'cge', 
    'okie', 'oks', 'sige po', 'noted po', 'araits', 'gege', 'g', 'oo', 'opo',
    'ayos', 'umaayos', 'sure', 'fine', 'no problem', 'understands', 'understood',
    'sigt', 'sigesige', 'sge', 'sgepo'
];

//Greetings
$pool_greetings = [
    'hi', 'hello', 'hey', 'yo', 'magandang araw', 'magandang umaga', 'magandang hapon', 
    'magandang gabi', 'good morning', 'good afternoon', 'good evening', 'wazzup', 
    'kamusta', 'musta', 'uy', 'hoy', 'hello po', 'hi po', 'kamusta po', 'greetings',
    'hello-bot', 'owshie', 'eyy', 'oy'
];

//Gratitude
$pool_gratitude = [
    'salamat', 'thank you', 'thanks', 'ty', 'thx', 'maraming salamat', 'salamat po', 
    'maraming salamat po', 'thankyou', 'tysm', 'grateful', 'appreciate', 'appreciation',
    'tenkyu', 'tenks', 'thnkz'
];

//Emergency Triggers
$pool_emergency = [
    'heart attack', 'stroke', 'aksidente', 'nag-aagaw buhay', 'bendahe', 
    'unconscious', 'walang malay', 'hinimatay na matagal', 'seizure', 'kombulsyon', 'accident',
    'nasagasaan', 'nataga', 'nabaril', 'nagco-collapse', 'collapsing',
    'atake', 'inaatake', 'hinto ang puso', 'nagdurugo ang ulo', 'cancer'
];

//SPECIFIC DOCTOR SEARCH POOLS
$pool_docs_general = ['macas', 'reymar', 'bayani', 'khane'];
$pool_docs_pedia = ['tomadong', 'johanna'];
$pool_docs_obgyn = ['garduque', 'jody'];
$pool_docs_internal = ['arcenal', 'jonalyn', 'pagsolingan', 'serj', 'serg'];

//PEDIA
$pool_pediatrics = [
    'baby', 'child', 'toddler', 'pediatric', 'kid', 'anak', 'bata', 'sanggol', 
    'pedia', 'pediatrician', 'bulutong', 'tigdas', 'tuli', 'kids', 'babies', 
    'infant', 'newborn', 'bulate', 'purga', 'bebe', 'kinatasan', 'growth', 
    'development', 'pediatrics', 'gatas', 'feeding', 'bakuna', 'immunization', 
    'tule', 'tinutuli', 'tore', 'infants', 'adolescents', 'child vaccination', 
    'child vaccine', 'growth monitoring', 'childhood illnesses', 'nutrition counseling', 
    'poor appetite', 'walang gana kumain', 'ayaw kumain', 'lagnat ng bata', 'ubo ng bata',
    'skin rashes', 'pantal bata', 'bungang araw', 'rashes'
];

//OB
$pool_obgyn = [
    'diet', 'pregnant', 'pregnancy', 'period', 'menstrual', 'cramp', 'ob-gyn', 
    'gynecology', 'buntis', 'regla', 'mens', 'obgyn', 'nagbubuntis', 
    'delayed', 'miscarriage', 'nakunan', 'raspa', 'pcos', 'dysmenorrhea', 
    'menopause', 'ob gyne', 'buntis ba ako', 'contraceptive', 'ultrasound', 
    'prenatal', 'postnatal', 'maternity', 'matres', 'obgyne', 'pills', 'spotting', 
    'puson', 'masakit ang puson', 'kababaihan', 'babae', 'ovary', 'cervical', 
    'pap smear', 'breastfeed', 'prenatal care', 'postnatal care', 'pregnancy checkup', 
    'family planning', 'women\'s health', 'cervical cancer', 'screening', 'missed periods', 
    'irregular periods', 'irregular mens', 'pregnancy concerns', 'pelvic pain', 
    'abnormal vaginal discharge', 'vaginal discharge', 'heavy menstrual bleeding', 
    'painful menstrual bleeding', 'heavy bleeding'
];

//INTERNAL MEDICINE 
$pool_internal_med = [
    'hypertension', 'diabetes', 'chronic', 'liver', 'atay', 'lapay', 'baga', 'lung', 'lungs',
    'adult illnesses', 'high blood pressure management', 'diabetes diagnosis', 'diabetes care', 
    'respiratory disease', 'asthma', 'pneumonia', 'unexplained fatigue', 'pagkapagod', 
    'high blood sugar', 'sugar level', 'chest pain', 'shortness of breath', 'hirap huminga', 
    'hindi makahinga', 'hapo', 'hingal', 'persistent cough', 'matagal na ubo', 'frequent headaches', 
    'dizziness', 'weakness', 'panghihina', 'hilo', 'nahihilo', 'regular medical check-ups'
];

//GENERAL MEDICINE
$pool_general_med = [
    'im not feeling well', 'not feeling well', 'feeling well', 'unwell', 'ill', 'sick',
    'masama ang pakiramdam', 'hindi mabuti ang pakiramdam', 'may sakit', 'sakit', 'saket',
    'nilalagnat', 'lagnat', 'lagnot', 'nilalagnot', 'sinisipon', 'sipon', 'sepon', 'sinisepon',
    'umuubo', 'ubo', 'oba', 'umuoba', 'trankaso', 'trangkaso', 'sakit ng ulo', 'saket ng ulo',
    'checkup', 'check-up', 'feverish', 'general', 'pantal', 'allergy', 'kumonsulta', 'pacheckup', 
    'physical exam', 'medical certificate', 'med cert', 'sore throat', 'paos', 'masakit ang lalamunan', 
    'beke', 'gripe', 'pagtatae', 'tatae', 'nagtatae', 'tiyan', 'sikmura', 'sumasakit ang tiyan', 
    'sumasakit tiyan', 'kabag', 'lalamunan', 'tuyo ang lalamunan', 'boses', 'sakit ng katawan', 
    'pagod', 'nanghihina', 'hina', 'suka', 'nagsusuka', 'dumi', 'tae', 'tae ng tae', 'constipated', 
    'purgahin', 'kati', 'kumakati', 'kati-kati', 'alelegy', 'medcert', 'fit to work', 'clearance', 
    'general medicine', 'general medical consultation', 'fever or persistent cough', 
    'headache or dizziness', 'stomach pain', 'digestive problems', 'routine health check-up'
];

//FORM SPECIFIC APPOINTMENT UX QUERIES
$pool_form_purpose = ['followup', 'follow-up', 'vaccination', 'bakuna', 'consultation', 'purpose'];
$pool_form_consult_type = ['online', 'virtual checkup', 'video call', 'teleconsult', 'telemedicine', 'face to face', 'in person', 'consultation type'];
$pool_form_dates = ['walang slot', 'not available', 'schedule of doctors', 'clinics days', 'pili ng date', 'doctor schedule', 'date field'];

//FAQ Support
$pool_system_queries = [
    'how to book', 'paano mag book', 'appointment process', 'bayad', 'payment', 
    'magkano', 'how much', 'schedule', 'oras', 'open ba kayo', 'clinic hours', 
    'cancelled', 'cancel appointment', 'reschedule', 'doktor', 'doctors list',
    'magkano pacheckup', 'rates', 'fees', 'magkano po', 'how to use', 'paano gamitin',
    'can we speak english', 'english please', 'magtagalog tayo', 'tagalog please'
];



//2.SMART REGEX MATCHING LOGIC

function matchesPool($message, $pool_array) {
    foreach ($pool_array as $keyword) {
        
        if (strlen($keyword) <= 3) {
            if (preg_match('/\b' . preg_quote($keyword, '/') . '\b/u', $message)) {
                return true;
            }
        } else {
            
            if (preg_match('/\b' . preg_quote($keyword, '/') . '\b/u', $message)) {
                return true;
            }
            if (mb_strpos($message, $keyword) !== false) {
                return true;
            }
        }
    }
    return false;
}

//Simple Language Classifier
$has_tagalog_telltales = preg_match('/\b(ko|si|ang|aking|ba|po|opo|meron|may|mga|sa|na|at|din|rin|sakit|saket|lagnat|ubo)\b/u', $message);
$has_english_telltales = preg_match('/\b(im|i|am|not|feeling|well|have|a|my|is|the|you|he|she|they|feel|sick|fever|cough)\b/u', $message);

if ($has_tagalog_telltales) {
    $is_tagalog = true;
} elseif ($has_english_telltales) {
    $is_tagalog = false;
} else {
    $is_tagalog = false;
}



//3.LOGIC ROUTING & SYSTEM RESPONSES

//1.CURSE WORDS INTERCEPTION
if (matchesPool($message, $pool_profanity)) {
    $reply = "Let's keep our communication respectful. SwiftCare is a professional medical platform. How can I assist you with your health today?";
}

//2.EMERGENCY BYPASS
elseif (matchesPool($message, $pool_emergency)) {
    $reply = "⚠️ <b>🚨 EMERGENCY WARNING / BABALA:</b><br><br>" .
             "Kung nakakaranas ng matinding pinsala, stroke, labis na pagdurugo, o pagkawala ng malay, <b>huwag na pong mag-book sa app ngayon</b>.<br><br>" .
             "Mangyaring pumunta agad sa pinakamalapit na Emergency Room (ER) o tumawag sa emergency medical hotlines. Ang kaligtasan ninyo ang pinakamahalaga.";
}

//3.EXPLICIT LANGUAGE SWITCHING REQUESTS
elseif (preg_match('/\b(english|can we speak english|english please|speak english)\b/u', $message)) {
    $reply = "Yes, absolutely! We can communicate in English. How can I help you with your symptoms or booking concerns today?";
}
elseif (preg_match('/\b(tagalog|magtagalog tayo|tagalog please|pwedeng tagalog|speak tagalog)\b/u', $message)) {
    $reply = "Walang problema! Maaari tayong mag-usap sa Tagalog. Ano ang iyong medikal na kailangan o nararamdaman ngayon?";
}

//4.CASUAL ACKNOWLEDGMENTS
elseif (matchesPool($message, $pool_acknowledgments)) {
    $reply = "Sige po! Just type your symptoms or concern anytime, and I'll route you to the right department or help you with your booking form.";
}

//5.GRATITUDE
elseif (matchesPool($message, $pool_gratitude)) {
    $reply = "Walang anuman! You're very welcome. 😊 SwiftCare is always here to keep your health journey smooth and swift. Let me know if you need anything else!";
}

//6.GREETINGS
elseif (matchesPool($message, $pool_greetings)) {
    if (!$is_tagalog) {
        $reply = "Hello! Welcome to SwiftCare Clinic Assistant. I am SwiftBot! What health concerns or symptoms are you experiencing today so I can guide you to the right doctor?";
    } else {
        $reply = "Hello! Magandang araw sa iyo. Welcome to SwiftCare Clinic Assistant. Ako si SwiftBot! Ano po ang nararamdaman ninyo o ng inyong kasama ngayon? Matutulungan ko kayong pumili ng tamang doktor.";
    }
}

//7.DIRECT DOCTOR QUERY MATCHING
elseif (matchesPool($message, $pool_docs_general)) {
    if (!$is_tagalog) {
        $reply = "🩺 <b>General Medicine Department:</b> <b>Dr. Reymar Macas</b> and <b>Dr. Khane Bayani</b> are available here for common adult concerns, routine check-ups, and fevers.<br><br>" .
                 "<a href='patient_request consultation.php' class='btn btn-sm btn-primary' style='background-color:#02529c; color:white; text-decoration:none; padding:6px 12px; border-radius:4px; display:inline-block; font-size:12px; margin-top:5px;'>Book with General Medicine</a>";
    } else {
        $reply = "🩺 <b>General Medicine Department:</b> Si <b>Dr. Reymar Macas</b> at <b>Dr. Khane Bayani</b> ay nakatalaga rito para sa mga karaniwang sakit (lagnat, ubo, o pananakit ng tiyan).<br><br>" .
                 "<a href='patient_request consultation.php' class='btn btn-sm btn-primary' style='background-color:#02529c; color:white; text-decoration:none; padding:6px 12px; border-radius:4px; display:inline-block; font-size:12px; margin-top:5px;'>Book with General Medicine</a>";
    }
}
elseif (matchesPool($message, $pool_docs_pedia)) {
    if (!$is_tagalog) {
        $reply = "👶 <b>Pediatrics Department:</b> <b>Dr. Johanna Tomadong</b> and <b>Dr. Sarah Jane Taroy</b> are our medical practitioners managing child health care and development.<br><br>" .
                 "<a href='patient_request consultation.php' class='btn btn-sm btn-primary' style='background-color:#02529c; color:white; text-decoration:none; padding:6px 12px; border-radius:4px; display:inline-block; font-size:12px; margin-top:5px;'>Book a Pediatrician</a>";
    } else {
        $reply = "👶 <b>Pediatrics Department:</b> Sina <b>Dr. Johanna Tomadong</b> at <b>Dr. Sarah Jane Taroy</b> ang aming mga specialista sa kalusugan ng bata.<br><br>" .
                 "<a href='patient_request consultation.php' class='btn btn-sm btn-primary' style='background-color:#02529c; color:white; text-decoration:none; padding:6px 12px; border-radius:4px; display:inline-block; font-size:12px; margin-top:5px;'>Book a Pediatrician</a>";
    }
}
elseif (matchesPool($message, $pool_docs_obgyn)) {
    if (!$is_tagalog) {
        $reply = "🤰 <b>OB-GYN Department:</b> <b>Dr. Jody Garduque</b> and <b>Dr. Sarah Jane Taroy</b> manage prenatal evaluations, family planning, and reproductive healthcare lines.<br><br>" .
                 "<a href='patient_request consultation.php' class='btn btn-sm btn-primary' style='background-color:#02529c; color:white; text-decoration:none; padding:6px 12px; border-radius:4px; display:inline-block; font-size:12px; margin-top:5px;'>Book an OB-GYN Specialist</a>";
    } else {
        $reply = "🤰 <b>OB-GYN Department:</b> Sina <b>Dr. Jody Garduque</b> at <b>Dr. Sarah Jane Taroy</b> ang namamahala sa pregnancy and women's reproductive care.<br><br>" .
                 "<a href='patient_request consultation.php' class='btn btn-sm btn-primary' style='background-color:#02529c; color:white; text-decoration:none; padding:6px 12px; border-radius:4px; display:inline-block; font-size:12px; margin-top:5px;'>Book an OB-GYN Specialist</a>";
    }
}
elseif (matchesPool($message, $pool_docs_internal)) {
    if (!$is_tagalog) {
        $reply = "🫀 <b>Internal Medicine Department:</b> <b>Dr. Jonalyn Arcenal</b> and <b>Dr. Serj Pagsolingan</b> specialize in chronic conditions like diabetes and high blood pressure.<br><br>" .
                 "<a href='patient_request consultation.php' class='btn btn-sm btn-primary' style='background-color:#02529c; color:white; text-decoration:none; padding:6px 12px; border-radius:4px; display:inline-block; font-size:12px; margin-top:5px;'>Book Internal Medicine</a>";
    } else {
        $reply = "🫀 <b>Internal Medicine Department:</b> Sina <b>Dr. Jonalyn Arcenal</b> at <b>Dr. Serj Pagsolingan</b> ang mga eksperto para sa altapresyon, puso, at diabetes.<br><br>" .
                 "<a href='patient_request consultation.php' class='btn btn-sm btn-primary' style='background-color:#02529c; color:white; text-decoration:none; padding:6px 12px; border-radius:4px; display:inline-block; font-size:12px; margin-top:5px;'>Book Internal Medicine</a>";
    }
}

//8.APPOINTMENT PURPOSE TIP
elseif (matchesPool($message, $pool_form_purpose)) {
    if (!$is_tagalog) {
        $reply = "💡 <b>Booking Tip:</b> When setting up your session, choose the exact match for the <b>Purpose</b> field (Check-up, Follow-up, or Vaccination) so your doctor can pull up your files beforehand!";
    } else {
        $reply = "Camp 🩺 <b>Form Booking Tip:</b> When filling out your request form, look for the <b>Purpose</b> field at the bottom right. Make sure to match it with your target goal (such as <i>Check-up, Consultation, Follow-up, or Vaccination</i>) so our doctors can prepare your exact records in advance!";
    }
}

//9.CONSULTATION TYPE TIP
elseif (matchesPool($message, $pool_form_consult_type)) {
    if (!$is_tagalog) {
        $reply = "💻 <b>Mode Selection:</b> You can pick <b>In Person</b> for a real-life physical visit at our medical facility or <b>Online</b> for virtual video communication assessments.";
    } else {
        $reply = "💻 <b>Consultation Type Options:</b> SwiftCare supports both delivery modes! Under the <b>Consultation Type</b> dropdown at the very bottom of the form, you can choose <b>In Person</b> for a physical visit or <b>Online</b> for a virtual telemedicine video call appointment.";
    }
}

//10.DYNAMIC DOCTOR SLOTS BY DATE
elseif (matchesPool($message, $pool_form_dates)) {
    if (!$is_tagalog) {
        $reply = "🗓️ <b>Dynamic Schedule Notice:</b> The doctor dropdown populates slots depending purely on the <b>Date</b> you select first on the calendar field. Check alternative dates if your doctor doesn't appear!";
    } else {
        $reply = "🗓️ <b>Doctor Availability Notice:</b> On our appointment form, the <b>Doctor</b> dropdown changes dynamically depending on the <b>Date</b> you select! If you cannot find your specific doctor, try selecting an alternate date field to see their corresponding shift days.";
    }
}

//11.PEDIATRICS ROUTING
elseif (matchesPool($message, $pool_pediatrics)) {
    if (!$is_tagalog) {
        $reply = "👶 <b>Pediatrics Department (Infant & Child Care):</b><br><br>" .
                 "It looks like your concern is regarding a child's health metrics (cough, infant fevers, vaccination tracking). Here are our dedicated Pediatricians:<br><br>" .
                 "👩‍⚕️ <b>Dr. Taroy, Sarah Jane</b><br>" .
                 "👩‍⚕️ <b>Dr. Tomadong, Johanna</b><br><br>" .
                 "<a href='patient_request consultation.php' class='btn btn-sm btn-primary' style='background-color:#02529c; color:white; text-decoration:none; padding:6px 12px; border-radius:4px; display:inline-block; font-size:12px;'>Fill Appointment Form</a>";
    } else {
        $reply = "👶 <b>Pediatrics Department (Para sa mga Infant, Bata, at Adolescents):</b><br><br>" .
                 "Mukhang ang inyong concern ay para sa kalusugan ng bata (gaya ng lagnat, ubo, rashes, bakuna, o mahinang gana kumain). Narito ang aming mga Pediatricians:<br><br>" .
                 "👩‍⚕️ <b>Dr. Taroy, Sarah Jane</b><br>" .
                 "👩‍⚕️ <b>Dr. Tomadong, Johanna</b><br><br>" .
                 "<a href='patient_request consultation.php' class='btn btn-sm btn-primary' style='background-color:#02529c; color:white; text-decoration:none; padding:6px 12px; border-radius:4px; display:inline-block; font-size:12px;'>Fill Appointment Form</a>";
    }
}

//12.OBSTETRICS AND GYNECOLOGY ROUTING
elseif (matchesPool($message, $pool_obgyn)) {
    if (!$is_tagalog) {
        $reply = "🤰 <b>Obstetrics and Gynecology (OB-GYN / Women's Health):</b><br><br>" .
                 "For maternity consultations, missed or irregular periods, pregnancy planning profiles, and family planning solutions:<br><br>" .
                 "👩‍⚕️ <b>Dr. Garduque, Jody</b><br>" .
                 "👩‍⚕️ <b>Dr. Taroy, Sarah Jane</b><br><br>" .
                 "<a href='patient_request consultation.php' class='btn btn-sm btn-primary' style='background-color:#02529c; color:white; text-decoration:none; padding:6px 12px; border-radius:4px; display:inline-block; font-size:12px;'>Fill Appointment Form</a>";
    } else {
        $reply = "🤰 <b>Obstetrics and Gynecology (OB-GYN / Kalusugan ng Kababaihan):</b><br><br>" .
                 "Para sa mga concerns ukol sa pregnancy, family planning, missed or irregular periods, pelvic pain, o heavy bleeding, narito ang aming mga specialized specialists:<br><br>" .
                 "👩‍⚕️ <b>Dr. Garduque, Jody</b><br>" .
                 "👩‍⚕️ <b>Dr. Taroy, Sarah Jane</b><br><br>" .
                 "<a href='patient_request consultation.php' class='btn btn-sm btn-primary' style='background-color:#02529c; color:white; text-decoration:none; padding:6px 12px; border-radius:4px; display:inline-block; font-size:12px;'>Fill Appointment Form</a>";
    }
}

//13.INTERNAL MEDICINE ROUTING
elseif (matchesPool($message, $pool_internal_med)) {
    if (!$is_tagalog) {
        $reply = "🫀 <b>Internal Medicine (Adult Chronic Management):</b><br><br>" .
                 "If you require assistance managing persistent high blood pressure, complex respiratory conditions like asthma, or blood sugar fluctuations:<br><br>" .
                 "👩‍⚕️ <b>Dr. Arcenal, Jonalyn</b><br>" .
                 "👨‍⚕️ <b>Dr. Pagsolingan, Serj</b><br><br>" .
                 "<a href='patient_request consultation.php' class='btn btn-sm btn-primary' style='background-color:#02529c; color:white; text-decoration:none; padding:6px 12px; border-radius:4px; display:inline-block; font-size:12px;'>Fill Appointment Form</a>";
    } else {
        $reply = "🫀 <b>Internal Medicine (Para sa mga Sakit ng Matatanda o Chronic Conditions):</b><br><br>" .
                 "Kung nakakaranas ng matagal na panghihina, alta-presyon, diabetes management, hika/pneumonia, o pabalik-balik na pananakit ng dibdib, kumonsulta sa aming Internal Medicine physicians:<br><br>" .
                 "👩‍⚕️ <b>Dr. Arcenal, Jonalyn</b><br>" .
                 "👨‍⚕️ <b>Dr. Pagsolingan, Serj</b><br><br>" .
                 "<a href='patient_request consultation.php' class='btn btn-sm btn-primary' style='background-color:#02529c; color:white; text-decoration:none; padding:6px 12px; border-radius:4px; display:inline-block; font-size:12px;'>Fill Appointment Form</a>";
    }
}

//14.GENERAL MEDICINE ROUTING
elseif (matchesPool($message, $pool_general_med)) {
    if (!$is_tagalog) {
        $reply = "🩺 <b>General Medicine (Common Illnesses & Physical Exams):</b><br><br>" .
                 "For fevers, persistent coughs, common colds, digestive problems, or when processing medical clearances:<br><br>" .
                 "👨‍⚕️ <b>Dr. Macas, Reymar</b><br>" .
                 "👩‍⚕️ <b>Dr. Bayani, Khane</b><br><br>" .
                 "<a href='patient_request consultation.php' class='btn btn-sm btn-primary' style='background-color:#02529c; color:white; text-decoration:none; padding:6px 12px; border-radius:4px; display:inline-block; font-size:12px;'>Fill Appointment Form</a>";
    } else {
        $reply = "🩺 <b>General Medicine (Karaniwang Sakit, Pananakit ng Tiyan, o Physical Exams):</b><br><br>" .
                 "Para sa lagnat, ubo't sipon, pananakit ng tiyan o digestive concerns, sakit ng ulo, o kaya ay routine preventive health check-up, lumapit sa aming General Medicine doctors:<br><br>" .
                 "👨‍⚕️ <b>Dr. Macas, Reymar</b><br>" .
                 "👩‍⚕️ <b>Dr. Bayani, Khane</b><br><br>" .
                 "<a href='patient_request consultation.php' class='btn btn-sm btn-primary' style='background-color:#02529c; color:white; text-decoration:none; padding:6px 12px; border-radius:4px; display:inline-block; font-size:12px;'>Fill Appointment Form</a>";
    }
}

//15.FAQS SUPPORT
elseif (matchesPool($message, $pool_system_queries)) {
    if (!$is_tagalog) {
        $reply = "ℹ️ <b>Frequently Asked Questions (System Help):</b><br><br>" .
                 "1️⃣ <b>How to book?</b> Click the link option provided down below or access the top navbar area.<br>" .
                 "2️⃣ <b>Why are doctors shifting?</b> The dropdown list changes depending strictly on the chosen calendar <b>Date</b>.<br><br>" .
                 "<a href='patient_request consultation.php' class='btn btn-sm btn-primary' style='background-color:#02529c; color:white; text-decoration:none; padding:6px 12px; border-radius:4px; display:inline-block; font-size:12px;'>Go to Booking Form</a>";
    } else {
        $reply = "ℹ️ <b>Frequently Asked Questions (SwiftCare System Help):</b><br><br>" .
                 "1️⃣ <b>Paano mag-book?</b> Piliin ang link sa ibaba o i-click ang <b>'Request Consultation'</b> sa inyong navigation top header.<br>" .
                 "2️⃣ <b>Bakit iba ang doktor?</b> Ang listahan ng doktor ay gumagalaw depende sa pipiliing <b>Petsa (Date)</b>.<br>" .
                 "3️⃣ <b>Paano kumuha ng medical files?</b> Piliin ang <b>'Request Medical Documents'</b> tab sa inyong header panel.<br><br>" .
                 "<a href='patient_request consultation.php' class='btn btn-sm btn-primary' style='background-color:#02529c; color:white; text-decoration:none; padding:6px 12px; border-radius:4px; display:inline-block; font-size:12px;'>Go to Booking Form</a>";
    }
}

//16.MULTI-LINGUAL FALLBACK
else {
    if (!$is_tagalog) {
        $reply = "🤔 <b>I didn't quite catch those details.</b><br><br>" .
                 "Could you please specify your symptoms or describe your concern? Let me know if it's for a child, a pregnancy checkup, adult general concerns, or if you're looking for a specific doctor's availability.";
    } else {
        $reply = "🤔 <b>Paumanhin, hindi ko lubos na nakuha ang iyong mensahe.</b><br><br>" .
                 "Maaari mo bang detalyahin ang iyong nararamdaman? Sabihin sa akin kung ito ba ay para sa bata, lagnat matanda, buntis, o kung may partikular ka pong doktor na hinahanap sa aming klinika.<br><br>" .
                 "<i>Halimbawa: 'buntis checkup', 'pedia for baby'.</i>";
    }
}


// 4. JSON RESPONSE DELIVERY

echo json_encode(['reply' => $reply]);
exit();
?>