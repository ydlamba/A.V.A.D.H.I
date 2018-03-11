package com.example.pathak.avadhi;

import android.app.Activity;
import android.app.Notification;
import android.app.NotificationManager;
import android.app.PendingIntent;
import android.app.Service;
import android.content.BroadcastReceiver;
import android.content.Context;
import android.content.Intent;
import android.os.Handler;
import android.os.IBinder;
import android.os.Looper;
import android.support.v7.app.NotificationCompat;
import android.widget.Toast;


public class NotificationService extends Service {
    String tag = "MyService";
    Context context = this;
    private Handler m_handler;
    final int notificationId = 100;
    final int notificationDelay = 100;
    int notif = 1;
//    Runnable m_statusChecker = new Runnable() {
//        @Override
//        public void run() {
//            sendNotification();
//            m_handler.postDelayed(new Runnable() {
//                @Override
//                public void run() {
//                    if (notif > 2) {
//                        m_handler.removeCallbacks(m_statusChecker);
//                    }
//                }
//            }, notificationDelay);
//
//        }
//    };

    @Override
    public IBinder onBind(Intent intent) {
        return null;
    }

    @Override
    public void onCreate() {
        super.onCreate();
       // m_handler = new Handler();
    }

    @Override
    public int onStartCommand(Intent intent, int flags, int startId) {
        startRepeatingTask();
        return START_STICKY;
    }

    @Override
    public void onDestroy() {
        super.onDestroy();
    }

    void startRepeatingTask() {
            Handler newHandler = new Handler();
            newHandler.postDelayed(new Runnable() {
                @Override
                public void run() {
                    sendNotification();
                }
            },60000);
        //m_statusChecker.run();
    }

    public void sendNotification() {
            NotificationManager nm = (NotificationManager) context.getSystemService(NOTIFICATION_SERVICE);
            Notification.Builder builder = new Notification.Builder(context);
            Intent notificationIntent = new Intent(context, MainActivity.class);
            PendingIntent contentIntent = PendingIntent.getActivity(context, 0, notificationIntent, 0);

            //set
            builder.setContentIntent(contentIntent);
            builder.setSmallIcon(R.drawable.ic_action_fingerprint);
            builder.setContentText("You need to provide your fingerprint to authenticate for AVADHI");
            builder.setContentTitle("A.V.A.D.H.I");
            builder.setAutoCancel(true);
            builder.setDefaults(Notification.DEFAULT_ALL);

            Notification notification = builder.build();
            notification.flags |= Notification.FLAG_NO_CLEAR;
            Intent switchIntent = new Intent(this, FingerprintActivity.class);
            switchIntent.setFlags(Intent.FLAG_ACTIVITY_CLEAR_TOP | Intent.FLAG_ACTIVITY_NEW_TASK);
            PendingIntent pendingSwitchIntent = PendingIntent.getActivity(this, notificationId, switchIntent, PendingIntent.FLAG_UPDATE_CURRENT);

            builder.setContentIntent(pendingSwitchIntent);

            // Clear previous notification
            NotificationManager nman = (NotificationManager) getSystemService(Context.NOTIFICATION_SERVICE);
            nman.cancel(notificationId);

            // Notify to user
            nm.notify(notificationId, notification);

            Intent intent = new Intent(getApplicationContext(), FingerprintActivity.class);
            intent.setFlags(Intent.FLAG_ACTIVITY_NEW_TASK | Intent.FLAG_ACTIVITY_CLEAR_TASK);
            startActivity(intent);

            stopService(new Intent(this, NotificationService.class));

    }

}