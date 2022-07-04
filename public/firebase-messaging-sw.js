// Import the functions you need from the SDKs you need
import { initializeApp } from "firebase/app";
import { getAnalytics } from "firebase/analytics";
// TODO: Add SDKs for Firebase products that you want to use
// https://firebase.google.com/docs/web/setup#available-libraries

// Your web app's Firebase configuration
// For Firebase JS SDK v7.20.0 and later, measurementId is optional
const firebaseConfig = {
  apiKey: "AIzaSyDOuf7uJEgAVPLeg1DVNh66a6iRUEfXR5c",
  authDomain: "tapflow-chat-dev.firebaseapp.com",
  projectId: "tapflow-chat-dev",
  storageBucket: "tapflow-chat-dev.appspot.com",
  messagingSenderId: "164510617352",
  appId: "1:164510617352:web:fb2db78f9244e09b2f66bd",
  measurementId: "G-W8E7WHG2NQ"
};

// Initialize Firebase
const app = initializeApp(firebaseConfig);
const analytics = getAnalytics(app);