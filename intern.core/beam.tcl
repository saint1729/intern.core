set concretegrade M25
set steelgrade Fe415

set steel  { { Fe250 250.0 0.0031 }
             { Fe415 415.0 0.0038 }
             { Fe500 500.0 0.0042 } }


array set section_symbols {
      symin  {b D cc n1 phi_1 n2 phi_2 phi_s s_v nleg}
      symout {f_ck f_y d Ast pst Muc Mus Asv Vuc Vus Vu} 

      b      "Width (mm) :"
      D      "Overall depth (mm) :"
      cc     "Clear cover (mm) :"
      n1     "No. of long. bars :"
      phi_1  "Bar dia(mm) :"
      n2     "No. of long. bars :"
      phi_2  "Bar dia(mm) :"
      phi_s  "Stirrup dia (mm) :"
      nleg   "No. of stirrup legs :"
      s_v   "Stirrup spacing (mm) :"

      f_ck  "Concrete strength (N/mm^2) :"
      f_y   "Steel strength (N/mm^2) :"
      d     "Effective depth (mm) :"
      Ast   "Area of long. steel (mm^2) :"
      pst   "Percentage of steel :"
      Asv   "Area of shear reinf. (mm^2) :"

      Muc "Mom. of resist. due to concrete (kNm) : "
      Mus "Mom. of resist. due to steel (kNm) : "
      Vuc "Critical shear due to concrete (kN) :"
      Vus "Shear capacity due to stirrup (kN) :"
      Vu  "Total shear capacity (kN) :"

}

array set section {
      b  275
      D  650
      cc 30
      n1  4
      phi_1 25
      n2  0
      phi_2 0
      phi_s 8
      nleg 4
      s_v 150
      f_ck 25
      f_y  415
}


proc compute {} {
   global section concretegrade steelgrade
   if {$concretegrade == "M15"} {
       set section(f_ck) 15.0
   } elseif {$concretegrade == "M20"} {
       set section(f_ck) 20.0
   } elseif {$concretegrade == "M25"} {
       set section(f_ck) 25.0
   } elseif {$concretegrade == "M30"} {
       set section(f_ck) 30.0
   }

   if {$steelgrade == "Fe250"} {
       set section(f_y) 250.0
       set section(eps_su) 0.0031
   } elseif {$steelgrade == "Fe415"} {
       set section(f_y) 415.0
       set section(eps_su) 0.0038
   } elseif {$steelgrade == "Fe50"} {
       set section(f_y) 500.0
       set section(eps_su) 0.0042
   }



   set section(d) [expr $section(D)-$section(cc)-0.5*$section(phi_1)]
   set section(Ast) [expr $section(n1)*3.14159*$section(phi_1)*$section(phi_1)/4+ \
                          $section(n2)*3.14159*$section(phi_2)*$section(phi_2)/4]

   set section(pst) [expr 100.0*$section(Ast)/($section(b)*$section(D))]
   set section(Asv) [expr $section(nleg)*3.14159*$section(phi_s)*$section(phi_s)/4]
   set xumax_by_d   [expr  0.0035/(0.0035+$section(eps_su))]
   set pt_bal  [expr 100.0 * 0.36 * $section(f_ck) * $xumax_by_d / ( 0.87 * $section(f_y) )]
   set K [expr 0.36 * $xumax_by_d * ( 1.0 - 0.42 * $xumax_by_d )]
   set section(Muc) [expr $K*$section(f_ck)*$section(b)*$section(d)*$section(d)*1.0e-6]
   set R [expr (0.87*$section(f_y)*$section(pst)/100.0)*(1.0-1.005*($section(f_y)/$section(f_ck))*$section(pst)/100.0)]
   set section(Mus) [expr $R*$section(b)*$section(d)*$section(d)*1.0e-06]
   set tau_c_max [expr 0.62*sqrt($section(f_ck))]
   set beta [expr 0.8*$section(f_ck)/(6.89*$section(pst))]
   set tau_c [expr 0.85*sqrt(0.8*$section(f_ck))*(sqrt(1.0+5.0*$beta)-1.0)/(6.0*$beta)]
   set section(Asv) [expr  $section(nleg)*3.14159*$section(phi_s)*$section(phi_s)/4.0]
   set section(Vuc) [expr $tau_c*$section(b)*$section(d)*1.0e-03]
   set section(Vus) [expr 0.87*$section(f_y)*$section(Asv)*$section(d)*1.0e-03/$section(s_v)]
   set section(Vu) [expr $section(Vuc)+$section(Vus)]
}

proc main {} {
   global section section_symbols

   frame .pad -bg cyan
   pack  .pad 

   frame .cmd
   pack  .cmd -in .pad -fill y -side top

   button .quit -text "Quit" -command exit
   button .compute -text "Compute" -command { compute }
   pack   .quit .compute  -in .cmd -side left -expand true

   frame .inp -relief ridge -width 100 -height 100 -borderwidth 4  -bg cyan
   pack  .inp -in .pad -fill y -side left
   frame .out -relief ridge -width 100 -height 100 -borderwidth 4  -bg cyan
   pack  .out -in .pad -fill y -fill x 

   frame .inpLabel -bg yellow
   pack  .inpLabel -in .inp -fill both
   label .inpLabel.label -text "Input variables" -bg blue -fg red
   pack  .inpLabel.label -in .inpLabel  -fill x

   foreach e $section_symbols(symin) {
      frame .f$e -bg yellow
      pack  .f$e -in .inp -fill both
      label .f$e.label -text $section_symbols($e) -bg yellow
      entry .f$e.entry -width 10 -relief sunken -textvariable section($e)
      pack  .f$e.label -in .f$e -side left  -fill x
      pack  .f$e.entry -in .f$e -side right -fill x

      bind  .f$e.entry <Return> { compute }
   }

   frame .concrete -bg yellow
   pack  .concrete -in .inp -fill both

   label .concrete.label -text "Concrete:" -bg yellow
   pack .concrete.label  -side left -fill y

   radiobutton .concrete.m15 -text M15 -bg yellow -variable concretegrade -value M15 -command compute
   radiobutton .concrete.m20 -text M20 -bg yellow -variable concretegrade -value M20 -command compute
   radiobutton .concrete.m25 -text M25 -bg yellow -variable concretegrade -value M25 -command compute
   radiobutton .concrete.m30 -text M30 -bg yellow -variable concretegrade -value M30 -command compute
   pack .concrete.m15  .concrete.m20  .concrete.m25  .concrete.m30  -side left

   frame .steel -bg yellow
   pack  .steel -in .inp -fill both

   label .steel.label -text "   Steel:" -bg yellow
   pack .steel.label  -side left -fill y

   radiobutton .steel.fe250 -text Fe250 -bg yellow -variable steelgrade -value Fe250 -command compute
   radiobutton .steel.fe415 -text Fe415 -bg yellow -variable steelgrade -value Fe415 -command compute
   radiobutton .steel.fe500 -text Fe500 -bg yellow -variable steelgrade -value Fe500 -command compute
   pack .steel.fe250 .steel.fe415 .steel.fe500  -side left



   frame .outLabel -bg yellow
   pack  .outLabel -in .out -fill both
   label .outLabel.label -text "Results" -bg blue -fg red
   pack  .outLabel.label -in .outLabel  -fill x 

   foreach e $section_symbols(symout) {
      frame .f$e -bg yellow
      pack  .f$e -in .out -fill both
      label .f$e.label -text $section_symbols($e) -bg yellow
      entry .f$e.entry -width 10 -relief sunken -textvariable section($e)
      pack  .f$e.label -in .f$e -side left  -fill x
      pack  .f$e.entry -in .f$e -side right -fill x

      bind  .f$e.entry <Return> { compute }
   }
}

main 
