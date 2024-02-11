# -*- coding: ISO-8859-1 -*-
#
# generated by wxGlade 0.8.0 on Tue Oct 22 16:53:53 2019
#

import wx
# begin wxGlade: dependencies
# end wxGlade

# begin wxGlade: extracode
# end wxGlade


class MultiPointliste(wx.Dialog):
    def __init__(self, *args, **kwds):
        # begin wxGlade: MultiPointliste.__init__
        kwds["style"] = kwds.get("style", 0) | wx.DEFAULT_DIALOG_STYLE
        wx.Dialog.__init__(self, *args, **kwds)
        self.SetSize((564, 475))
        self.SetTitle("Pointliste Mehrfachauswahl")

        sizer_2 = wx.BoxSizer(wx.VERTICAL)

        self.list_ctrl_Pointliste = wx.ListCtrl(self, wx.ID_ANY, style=wx.LC_HRULES | wx.LC_REPORT | wx.LC_VRULES)
        self.list_ctrl_Pointliste.SetMinSize((461, 400))
        self.list_ctrl_Pointliste.AppendColumn("Point_ID", format=wx.LIST_FORMAT_LEFT, width=65)
        self.list_ctrl_Pointliste.AppendColumn("Tagname", format=wx.LIST_FORMAT_LEFT, width=135)
        self.list_ctrl_Pointliste.AppendColumn("Beschreibung", format=wx.LIST_FORMAT_LEFT, width=160)
        self.list_ctrl_Pointliste.AppendColumn("Schnittstelle", format=wx.LIST_FORMAT_LEFT, width=80)
        sizer_2.Add(self.list_ctrl_Pointliste, 1, wx.EXPAND, 0)

        grid_sizer_8 = wx.GridSizer(1, 2, 0, 0)
        sizer_2.Add(grid_sizer_8, 1, wx.EXPAND, 0)

        self.MultiPoints_uebernehmen = wx.Button(self, wx.ID_ANY, u"übernehmen")
        grid_sizer_8.Add(self.MultiPoints_uebernehmen, 0, wx.ALIGN_BOTTOM, 0)

        self.MultiPoints_abbrechen = wx.Button(self, wx.ID_ANY, "abbrechen")
        grid_sizer_8.Add(self.MultiPoints_abbrechen, 0, wx.ALIGN_BOTTOM | wx.ALIGN_RIGHT, 0)

        self.SetSizer(sizer_2)

        self.Layout()

        self.Bind(wx.EVT_BUTTON, self.evt_MultiPoints_uebernehmen, self.MultiPoints_uebernehmen)
        self.Bind(wx.EVT_BUTTON, self.evt_MultiPoints_abbrechen, self.MultiPoints_abbrechen)
        # end wxGlade

    def evt_MultiPoints_uebernehmen(self, event):  # wxGlade: MultiPointliste.<event_handler>
        global Tab
        if Tab == "Rekalkulator":
            Liste = wx.GetApp().Hauptrahmen.list_ctrl_1
            Liste.DeleteAllItems()
            Zeile = self.list_ctrl_Pointliste.GetFirstSelected()
            while Zeile != -1:
                Liste.Append([self.list_ctrl_Pointliste.GetItemText(Zeile, col=0), self.list_ctrl_Pointliste.GetItemText(Zeile, col=1), self.list_ctrl_Pointliste.GetItemText(Zeile, col=2), self.list_ctrl_Pointliste.GetItemText(Zeile, col=3)])
                Zeile = self.list_ctrl_Pointliste.GetNextSelected(Zeile)
        self.Close()

    def evt_MultiPoints_abbrechen(self, event):  # wxGlade: MultiPointliste.<event_handler>
        self.Close()
    def Liste_hinterlegen(self, Registerkarte):
        global Tab
        Tab = Registerkarte
# end of class MultiPointliste
