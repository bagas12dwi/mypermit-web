import "./bootstrap";

// Import the functions you need from the SDKs you need
import { initializeApp } from "firebase/app";
import { getAnalytics } from "firebase/analytics";
import { getMessaging, getToken, onMessage } from "firebase/messaging";
// TODO: Add SDKs for Firebase products that you want to use
// https://firebase.google.com/docs/web/setup#available-libraries

// Your web app's Firebase configuration
// For Firebase JS SDK v7.20.0 and later, measurementId is optional
const firebaseConfig = {
    apiKey: "AIzaSyAQ_6W5zP5TUHpqpmgvmuH1FQ7eP2aXRio",
    authDomain: "mypermit-bdccd.firebaseapp.com",
    projectId: "mypermit-bdccd",
    storageBucket: "mypermit-bdccd.appspot.com",
    messagingSenderId: "786995149817",
    appId: "1:786995149817:web:ddee02a0c926e1e5495b28",
    measurementId: "G-GPWJC58BW2",
};

// Initialize Firebase
const app = initializeApp(firebaseConfig);
const analytics = getAnalytics(app);
const messaging = getMessaging(app);

onMessage(messaging, (payload) => {
    console.log("Message received. ", payload);
    alert("ada notifikasi baru!");
});

getToken(messaging, {
    vapidKey:
        "BDUJSzfKFoDyRzPrP7ca2FGY90StHOgmSo4JkeGJE3CsV667gWyxcej8F_yIuGAbZZZ5gwHTuHwpJnEIG-P7NFQ",
})
    .then((currentToken) => {
        if (currentToken) {
            // Send the token to your server and update the UI if necessary
            // ...
            console.log(currentToken);
        } else {
            requestPermission();
            console.log(
                "No registration token available. Request permission to generate one."
            );
            // ...
        }
    })
    .catch((err) => {
        console.log("An error occurred while retrieving token. ", err);
        // ...
    });

function requestPermission() {
    Notification.requestPermission().then((permission) => {
        if (permission === "granted") {
            console.log("Notification permission granted.");
            // TODO(developer): Retrieve a registration token for use with FCM.
            // ...
        } else {
            console.log(
                "Silahkan aktifkan notifikasi untuk mendapatkan notifikasi terbaru dari kami!"
            );
        }
    });
}
