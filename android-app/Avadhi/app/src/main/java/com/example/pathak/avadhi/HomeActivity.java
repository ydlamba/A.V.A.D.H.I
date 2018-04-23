package com.example.pathak.avadhi;

import android.content.Intent;
import android.os.Bundle;
import android.support.v7.app.AppCompatActivity;
import android.support.v7.widget.Toolbar;


public class HomeActivity extends AppCompatActivity {

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_home);
        Intent myIntent = new Intent(HomeActivity.this, NotificationService.class);
        startService(myIntent);
        Intent deamonIntent = new Intent(HomeActivity.this, DeamonService.class);
        startService(deamonIntent);
    }

}