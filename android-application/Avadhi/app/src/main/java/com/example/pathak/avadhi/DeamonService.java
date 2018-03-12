package com.example.pathak.avadhi;


import android.annotation.SuppressLint;
import android.app.Service;
import android.content.Context;
import android.content.Intent;
import android.net.wifi.WifiInfo;
import android.net.wifi.WifiManager;
import android.os.Handler;
import android.os.IBinder;
import android.util.Log;
import java.lang.Runnable;

import java.io.IOException;
import java.io.OutputStreamWriter;
import java.net.DatagramPacket;
import java.net.DatagramSocket;
import java.net.InetAddress;
import java.net.NetworkInterface;
import java.net.Socket;
import java.util.Collections;
import java.util.List;


public class DeamonService extends Service {
    String tag="DeamonService";
    Context context = this;
    private Handler m_handler;
    final int logOutDelay = 60000;
    boolean shouldKillService = false;
    int broadcastPort = 7447;
    int tcpConnPort = 7777;

//    Runnable m_statusChecker = new Runnable() {
//        @Override
//        public void run() {
//            startDeamonScript();
//            m_handler.postDelayed(new Runnable() {
//                @Override
//                public void run() {
//                    if (shouldKillService) {
//                        m_handler.removeCallbacks(m_statusChecker);
//                        stopSelf();
//                    }
//                }
//            }, logOutDelay);
//
//        }
//    };

    @Override
    public IBinder onBind(Intent intent){
        return null;
    }
    @Override
    public void onCreate(){
        super.onCreate();
       // m_handler = new Handler();
    }

    @Override
    public int onStartCommand(Intent intent, int flags, int startId){
        startRepeatingTask();
        return START_STICKY;
    }

    @Override
    public void onDestroy(){
        super.onDestroy();
    }

    void startRepeatingTask() {
        new Thread(new Runnable(){
            @Override
        public void run() {
                runClient();
            }

        }).start();

        Handler newHandler = new Handler();
        newHandler.postDelayed(new Runnable() {
            @Override
            public void run() {
                stopSelf();
            }
        },logOutDelay);
        //m_statusChecker.run();
    }

    public void sendTcpPacket(InetAddress ipaddr, String mac_addr) throws IOException {
        Socket socket = null;
        try {
            socket = new Socket(ipaddr, tcpConnPort);
            OutputStreamWriter osw = new OutputStreamWriter(socket.getOutputStream(),
                    "UTF-8");
            osw.write(mac_addr, 0, mac_addr.length());
            osw.flush();
        }catch (Exception e) {
            Log.e("myTag", String.valueOf(e));
        } finally {
            assert socket != null;
            socket.close();
        }

    }

    public static String getMacAddr() {
        try {
            List<NetworkInterface> all = Collections.list(NetworkInterface.getNetworkInterfaces());
            for (NetworkInterface nif : all) {
                if (!nif.getName().equalsIgnoreCase("wlan0")) continue;

                byte[] macBytes = nif.getHardwareAddress();
                if (macBytes == null) {
                    return "";
                }

                StringBuilder res1 = new StringBuilder();
                for (byte b : macBytes) {
                    res1.append(Integer.toHexString(b & 0xFF) + ":");
                }

                if (res1.length() > 0) {
                    res1.deleteCharAt(res1.length() - 1);
                }
                return res1.toString();
            }
        } catch (Exception ex) {
            //handle exception
        }
        return "";
    }

    public void runClient() {

        String mac_address = getMacAddr();
        byte[] buffer = new byte[1024];
        Log.d("newag", mac_address);
        DatagramSocket ds = null;
        try {
            ds = new DatagramSocket(broadcastPort);
            DatagramPacket packet = new DatagramPacket(buffer, buffer.length);
            Log.d("mac address", mac_address);
            while (true) {
                ds.setSoTimeout(logOutDelay + 10000);
                ds.receive(packet);
                String rec_str = new String(packet.getData());
                Log.d("Over new tag", rec_str);
                InetAddress ipaddress = packet.getAddress();
                sendTcpPacket(ipaddress, mac_address);
            }

        }
        catch(Exception ex) {
            Log.e("myTag", String.valueOf(ex));
        } finally {
            assert ds != null;
            ds.close();
        }
    }



}